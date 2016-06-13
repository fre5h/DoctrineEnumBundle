<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\DForm;

use Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NotAChildType;
use Fresh\DoctrineEnumBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesserTest.
 *
 * @author Artem Genvald <genvaldartem@gmail.com>
 */
class EnumTypeGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testNullResultWhenClassMetadataNotFound()
    {
        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this->getMockBuilder('\Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser')
            ->disableOriginalConstructor()
            ->setMethods(['getMetadata'])
            ->getMock();

        $enumTypeGuesser->expects($this->once())
            ->method('getMetadata')
            ->willReturn(null);

        $this->assertNull($enumTypeGuesser->guessType('\stdClass', 'position'));
    }

    public function testNullResultWhenEnumTypeNotRegistered()
    {
        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this->getMockBuilder('\Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser')
                                ->disableOriginalConstructor()
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField'])
            ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('unregistered_enum_type');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType('\stdClass', 'position'));
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function testExceptionWhenClassDoesNotExist()
    {
        $managerRegistry = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'stub' => [
                'class' => '\Acme\Foo\Bar\Baz',
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this->getMockBuilder('\Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser')
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('stub');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType('\stdClass', 'position'));
    }

    public function testNullResultWhenIsNotChildOfAbstractEnumType()
    {
        $managerRegistry = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'NotAChildType' => [
                'class' => '\Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NotAChildType',
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this->getMockBuilder('\Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser')
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('NotAChildType');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType('\stdClass', 'position'));
    }

    public function testSuccessfulTypeGuessing()
    {
        $managerRegistry = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'BasketballPositionType' => [
                'class' => '\Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType',
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this->getMockBuilder('\Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser')
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder('\Doctrine\ORM\Mapping\ClassMetadataInfo')
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField', 'isNullable'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('BasketballPositionType');

        $metadata->expects($this->once())
                 ->method('isNullable')
                 ->willReturn(true);

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $typeGuess = new TypeGuess(
            LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\ChoiceType'),
            [
                'choices'  => BasketballPositionType::getChoices(),
                'required' => false,
            ],
            Guess::VERY_HIGH_CONFIDENCE
        );

        $this->assertEquals($typeGuess, $enumTypeGuesser->guessType('\stdClass', 'position'));
    }
}
