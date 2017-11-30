<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Validator;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum;
use PHPUnit\Framework\TestCase;

/**
 * EnumTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumTest extends TestCase
{
    public function testConstructor()
    {
        $constraint = new Enum([
            'entity' => BasketballPositionType::class,
        ]);

        $this->assertEquals(BasketballPositionType::getValues(), $constraint->choices);
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testMissedRequiredOption()
    {
        $constraint = new Enum();

        $this->assertEquals(['entity'], $constraint->getRequiredOptions());
    }

    public function testGetRequiredOptions()
    {
        $constraint = new Enum([
            'entity' => BasketballPositionType::class,
        ]);

        $this->assertEquals(['entity'], $constraint->getRequiredOptions());
    }

    public function testGetDefaultOption()
    {
        $constraint = new Enum([
            'entity' => BasketballPositionType::class,
        ]);

        $this->assertEquals('choices', $constraint->getDefaultOption());
    }
}
