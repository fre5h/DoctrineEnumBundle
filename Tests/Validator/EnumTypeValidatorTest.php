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

use Fresh\DoctrineEnumBundle\Exception\RuntimeException;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumTypeValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

/**
 * EnumValidatorTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class EnumTypeValidatorTest extends TestCase
{
    /** @var ExecutionContext|MockObject */
    private ExecutionContext|MockObject $context;

    private EnumTypeValidator $enumValidator;

    protected function setUp(): void
    {
        $this->enumValidator = new EnumTypeValidator();
        $this->context = $this->createMock(ExecutionContext::class);
    }

    protected function tearDown(): void
    {
        unset(
            $this->enumValidator,
            $this->context
        );
    }

    public function testValidateIncorrectConstraintClass(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Object of class .* is not instance of .*$/');

        $this->enumValidator->validate(BasketballPositionType::POINT_GUARD, new DummyConstraint());
    }

    public function testValidBasketballPositionType(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);

        $this->context
            ->expects(self::never())
            ->method('buildViolation')
        ;

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate(BasketballPositionType::SMALL_FORWARD, $constraint);
    }

    public function testInvalidBasketballPositionType(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);
        $constraintValidationBuilder = $this->createMock(ConstraintViolationBuilder::class);

        $constraintValidationBuilder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                [self::equalTo('{{ value }}'), self::equalTo('"Pitcher"')],
                [self::equalTo('{{ choices }}'), self::equalTo('"PG", "SG", "SF", "PF", "C"')]
            )
            ->willReturn($constraintValidationBuilder, $constraintValidationBuilder)
        ;

        $constraintValidationBuilder
            ->expects(self::once())
            ->method('setCode')
            ->willReturnSelf()
        ;

        $this->context
            ->expects(self::once())
            ->method('buildViolation')
            ->with(self::equalTo('The value you selected is not a valid choice.'))
            ->willReturn($constraintValidationBuilder)
        ;

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate('Pitcher', $constraint); // It's not a baseball =)
    }
}
