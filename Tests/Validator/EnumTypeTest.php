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

use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * EnumTypeTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumTypeTest extends TestCase
{
    #[Test]
    public function constructorWithRequiredArguments(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);

        self::assertEquals(BasketballPositionType::getValues(), $constraint->choices);
        self::assertTrue($constraint->strict);
    }

    #[Test]
    public function constructorWithAllArguments(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class, message: 'test', groups: ['foo'], payload: ['bar' => 'baz']);

        self::assertEquals(BasketballPositionType::getValues(), $constraint->choices);
        self::assertTrue($constraint->strict);
        self::assertEquals(BasketballPositionType::class, $constraint->entity);
        self::assertEquals('test', $constraint->message);
        self::assertEquals(['foo'], $constraint->groups);
        self::assertEquals(['bar' => 'baz'], $constraint->payload);
        self::assertNull($constraint->callback);
        self::assertFalse($constraint->multiple);
        self::assertNull($constraint->min);
        self::assertNull($constraint->max);
    }

    #[Test]
    public function notEnumType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('stdClass is not instance of Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType');

        new EnumType(entity: \stdClass::class);
    }

    #[Test]
    public function getDefaultOption(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);

        self::assertEquals('choices', $constraint->getDefaultOption());
    }
}
