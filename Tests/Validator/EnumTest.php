<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Validator;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum;

/**
 * EnumTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
        ]);

        $this->assertEquals(BasketballPositionType::getValues(), $constraint->choices);
    }

    /**
     * Test method missed required option `entity`
     *
     * @expectedException \Symfony\Component\Validator\Exception\MissingOptionsException
     */
    public function testMissedRequiredOption()
    {
        $constraint = new Enum();

        $this->assertEquals(['entity'], $constraint->getRequiredOptions());
    }

    /**
     * Test method `getRequiredOptions`
     */
    public function testGetRequiredOptions()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
        ]);

        $this->assertEquals(['entity'], $constraint->getRequiredOptions());
    }

    /**
     * Test method `getDefaultOption`
     */
    public function testGetDefaultOption()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
        ]);

        $this->assertEquals('choices', $constraint->getDefaultOption());
    }
}
