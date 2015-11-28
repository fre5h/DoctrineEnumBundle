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
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumValidator;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * EnumValidatorTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnumValidator $enumValidator ENUM validator
     */
    private $enumValidator;

    /**
     * @var ExecutionContext|\PHPUnit_Framework_MockObject_MockObject $context Context
     */
    private $context;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->enumValidator = new EnumValidator();

        $this->context = $this->getMockBuilder('Symfony\Component\Validator\ExecutionContext')
                              ->disableOriginalConstructor()
                              ->getMock();
    }

    /**
     * Test that creation of ENUM Constraint without type class throws ConstraintDefinitionException
     *
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function testExceptionEntityNotSpecified()
    {
        $constraint = new Enum([
            'entity' => null,
        ]);

        $this->enumValidator->validate(BasketballPositionType::POINT_GUARD, $constraint);
    }

    /**
     * Test valid basketball position
     */
    public function testValidBasketballPositionType()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
        ]);

        $this->context
             ->expects($this->never())
             ->method('addViolation');

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate(BasketballPositionType::SMALL_FORWARD, $constraint);
    }

    /**
     * Test invalid basketball position
     */
    public function testInvalidBasketballPositionType()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
        ]);

        $this->context
             ->expects($this->once())
             ->method('addViolation')
             ->with(
                 $this->equalTo('The value you selected is not a valid choice.'),
                 $this->equalTo(['{{ value }}' => '"Pitcher"'])
             );

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate('Pitcher', $constraint); // It's not a baseball =)
    }
}
