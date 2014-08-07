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

use Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Validator\Constraints\Enum;
use Fresh\DoctrineEnumBundle\Validator\Constraints\EnumValidator;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * EnumValidatorTest
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 *
 * @coversDefaultClass \Fresh\DoctrineEnumBundle\Validator\Constraints\EnumValidator
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
     * Set up ENUM validator
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
     * @test
     * @covers ::validate
     * @expectedException \Symfony\Component\Validator\Exception\ConstraintDefinitionException
     */
    public function exceptionEntityNotSpecified()
    {
        $this->enumValidator->validate(BasketballPositionType::POINT_GUARD, new Enum());
    }

    /**
     * Test valid basketball position
     *
     * @test
     * @covers ::validate
     */
    public function validBasketballPositionType()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType'
        ]);

        $this->context
             ->expects($this->never())
             ->method('addViolation');

        $this->enumValidator->initialize($this->context);
        $this->enumValidator->validate(BasketballPositionType::SMALL_FORWARD, $constraint);
    }

    /**
     * Test invalid basketball position
     *
     * @test
     * @covers ::validate
     */
    public function invalidBasketballPositionType()
    {
        $constraint = new Enum([
            'entity' => 'Fresh\DoctrineEnumBundle\Fixtures\DBAL\Types\BasketballPositionType'
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
