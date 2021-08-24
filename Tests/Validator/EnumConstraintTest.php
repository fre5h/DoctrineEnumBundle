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

use ArgumentCountError;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumConstraint;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * EnumConstraintTest.
 */
final class EnumConstraintTest extends TestCase
{
    public function testConstructor(): void
    {
        $constraint = new EnumConstraint(entity: BasketballPositionType::class);
        self::assertEquals(BasketballPositionType::getValues(), $constraint->choices);
    }

    public function testGetDefaultOption(): void
    {
        $constraint = new EnumConstraint(entity: BasketballPositionType::class);

        self::assertEquals('choices', $constraint->getDefaultOption());
    }
}
