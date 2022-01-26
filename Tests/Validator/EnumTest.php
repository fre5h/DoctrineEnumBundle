<?php
/*
 * This file is part of the FreshDoctrineEnumBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Fresh\DoctrineEnumBundle\Tests\Validator;

use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * EnumTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumTest extends TestCase
{
    public function testConstructor(): void
    {
        $constraint = new Enum(['entity' => BasketballPositionType::class]);

        self::assertEquals(BasketballPositionType::getValues(), $constraint->choices);
    }

    public function testMissedRequiredOption(): void
    {
        $this->expectException(MissingOptionsException::class);
        self::assertEquals(['entity'], (new Enum())->getRequiredOptions());
    }

    public function testGetRequiredOptions(): void
    {
        $constraint = new Enum(['entity' => BasketballPositionType::class]);

        self::assertEquals(['entity'], $constraint->getRequiredOptions());
    }

    public function testGetDefaultOption(): void
    {
        $constraint = new Enum(['entity' => BasketballPositionType::class]);

        self::assertEquals('choices', $constraint->getDefaultOption());
    }
}
