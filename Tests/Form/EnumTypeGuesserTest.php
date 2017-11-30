<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\DForm;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\InheritedType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NotAChildType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * EnumTypeGuesserTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class EnumTypeGuesserTest extends TestCase
{
    public function testNullResultWhenClassMetadataNotFound()
    {
        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetadata'])
            ->getMock();

        $enumTypeGuesser->expects($this->once())
            ->method('getMetadata')
            ->willReturn(null);

        $this->assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testNullResultWhenEnumTypeNotRegistered()
    {
        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
                                ->disableOriginalConstructor()
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField'])
            ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('unregistered_enum_type');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    /**
     * @expectedException \Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException
     */
    public function testExceptionWhenClassDoesNotExist()
    {
        $managerRegistry = $this->getMockBuilder(ManagerRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'stub' => [
                'class' => '\Acme\Foo\Bar\Baz',
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder(ClassMetadataInfo::class)
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('stub');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testNullResultWhenIsNotChildOfAbstractEnumType()
    {
        $managerRegistry = $this->getMockBuilder(ManagerRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'NotAChildType' => [
                'class' => NotAChildType::class,
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder(ClassMetadataInfo::class)
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('NotAChildType');

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $this->assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testSuccessfulTypeGuessingWithAncestor()
    {
        $managerRegistry = $this->getMockBuilder(ManagerRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'InheritedType' => [
                'class' => InheritedType::class,
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder(ClassMetadataInfo::class)
                         ->disableOriginalConstructor()
                         ->setMethods(['getTypeOfField', 'isNullable'])
                         ->getMock();

        $metadata->expects($this->once())
                 ->method('getTypeOfField')
                 ->willReturn('InheritedType');

        $metadata->expects($this->once())
                 ->method('isNullable')
                 ->willReturn(true);

        $enumTypeGuesser->expects($this->once())
                        ->method('getMetadata')
                        ->willReturn([$metadata]);

        $typeGuess = new TypeGuess(
            ChoiceType::class,
            [
                'choices' => InheritedType::getChoices(),
                'required' => false,
            ],
            Guess::VERY_HIGH_CONFIDENCE
        );

        $this->assertEquals($typeGuess, $enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testSuccessfulTypeGuessing()
    {
        $managerRegistry = $this->getMockBuilder(ManagerRegistry::class)
                                ->disableOriginalConstructor()
                                ->getMock();
        $registeredTypes = [
            'BasketballPositionType' => [
                'class' => BasketballPositionType::class,
            ]
        ];

        /** @var EnumTypeGuesser|\PHPUnit_Framework_MockObject_MockObject */
        $enumTypeGuesser = $this->getMockBuilder(EnumTypeGuesser::class)
                                ->setConstructorArgs([$managerRegistry, $registeredTypes])
                                ->setMethods(['getMetadata'])
                                ->getMock();

        $metadata = $this->getMockBuilder(ClassMetadataInfo::class)
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
            ChoiceType::class,
            [
                'choices' => BasketballPositionType::getChoices(),
                'required' => false,
            ],
            Guess::VERY_HIGH_CONFIDENCE
        );

        $this->assertEquals($typeGuess, $enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }
}
