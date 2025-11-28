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
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function validateIncorrectConstraintClass(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageMatches('/^Object of class .* is not instance of .*$/');

        $this->enumValidator->validate(BasketballPositionType::POINT_GUARD, new DummyConstraint());
    }

    #[Test]
    public function validBasketballPositionType(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);

        $this->context
            ->expects($this->never())
            ->method('buildViolation')
        ;

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate(BasketballPositionType::SMALL_FORWARD, $constraint);
    }

    #[Test]
    public function invalidBasketballPositionType(): void
    {
        $constraint = new EnumType(entity: BasketballPositionType::class);
        $constraintValidationBuilder = $this->createMock(ConstraintViolationBuilder::class);

        $matcher = $this->exactly(2);

        $constraintValidationBuilder
            ->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnCallback(function () use ($matcher) {
                return match ($matcher->numberOfInvocations()) {
                    1 => [$this->equalTo('{{ value }}'), $this->equalTo('"Pitcher"')],
                    2 => [$this->equalTo('{{ choices }}'), $this->equalTo('"PG", "SG", "SF", "PF", "C"')],
                };
            })
            ->willReturn($constraintValidationBuilder, $constraintValidationBuilder)
        ;

        $constraintValidationBuilder
            ->expects($this->once())
            ->method('setCode')
            ->willReturnSelf()
        ;

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($this->equalTo('The value you selected is not a valid choice.'))
            ->willReturn($constraintValidationBuilder)
        ;

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate('Pitcher', $constraint); // It's not a baseball =)
    }
}
