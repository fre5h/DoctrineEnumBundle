<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fresh\DoctrineEnumBundle\Tests\Form;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Fresh\DoctrineEnumBundle\Exception\EnumType\EnumTypeIsRegisteredButClassDoesNotExistException;
use Fresh\DoctrineEnumBundle\Form\EnumTypeGuesser;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\BasketballPositionType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\InheritedType;
use Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types\NotAChildType;
use PHPUnit\Framework\MockObject\MockObject;
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
    public function testNullResultWhenClassMetadataNotFound(): void
    {
        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn(null)
        ;

        self::assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testNullResultWhenEnumTypeNotRegistered(): void
    {
        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $metadata = $this
            ->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField'])
            ->getMock()
        ;

        $metadata
            ->expects(self::once())
            ->method('getTypeOfField')
            ->willReturn('unregistered_enum_type')
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn([$metadata])
        ;

        self::assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testExceptionWhenClassDoesNotExist(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $registeredTypes = [
            'stub' => [
                'class' => '\Acme\Foo\Bar\Baz',
            ]
        ];

        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->setConstructorArgs([$managerRegistry, $registeredTypes])
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $metadata = $this
            ->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField'])
            ->getMock()
        ;

        $metadata
            ->expects(self::once())
            ->method('getTypeOfField')
            ->willReturn('stub')
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn([$metadata])
        ;

        $this->expectException(EnumTypeIsRegisteredButClassDoesNotExistException::class);

        self::assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testNullResultWhenIsNotChildOfAbstractEnumType(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $registeredTypes = [
            'NotAChildType' => [
                'class' => NotAChildType::class,
            ]
        ];

        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->setConstructorArgs([$managerRegistry, $registeredTypes])
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $metadata = $this
            ->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField'])
            ->getMock()
        ;

        $metadata
            ->expects(self::once())
            ->method('getTypeOfField')
            ->willReturn('NotAChildType')
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn([$metadata])
        ;

        self::assertNull($enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testSuccessfulTypeGuessingWithAncestor(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $registeredTypes = [
            'InheritedType' => [
                'class' => InheritedType::class,
            ]
        ];

        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->setConstructorArgs([$managerRegistry, $registeredTypes])
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $metadata = $this
            ->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField', 'isNullable'])
            ->getMock()
        ;

        $metadata
            ->expects(self::once())
            ->method('getTypeOfField')
            ->willReturn('InheritedType')
        ;

        $metadata
            ->expects(self::once())
            ->method('isNullable')
            ->willReturn(true)
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn([$metadata])
        ;

        $typeGuess = new TypeGuess(
            ChoiceType::class,
            [
                'choices' => InheritedType::getChoices(),
                'required' => false,
            ],
            Guess::VERY_HIGH_CONFIDENCE
        );

        self::assertEquals($typeGuess, $enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }

    public function testSuccessfulTypeGuessing(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);

        $registeredTypes = [
            'BasketballPositionType' => [
                'class' => BasketballPositionType::class,
            ]
        ];

        /** @var EnumTypeGuesser|MockObject $enumTypeGuesser */
        $enumTypeGuesser = $this
            ->getMockBuilder(EnumTypeGuesser::class)
            ->setConstructorArgs([$managerRegistry, $registeredTypes])
            ->setMethods(['getMetadata'])
            ->getMock()
        ;

        $metadata = $this
            ->getMockBuilder(ClassMetadataInfo::class)
            ->disableOriginalConstructor()
            ->setMethods(['getTypeOfField', 'isNullable'])
            ->getMock()
        ;

        $metadata
            ->expects(self::once())
            ->method('getTypeOfField')
            ->willReturn('BasketballPositionType')
        ;

        $metadata
            ->expects(self::once())
            ->method('isNullable')
            ->willReturn(true)
        ;

        $enumTypeGuesser
            ->expects(self::once())
            ->method('getMetadata')
            ->willReturn([$metadata])
        ;

        $typeGuess = new TypeGuess(
            ChoiceType::class,
            [
                'choices' => BasketballPositionType::getChoices(),
                'required' => false,
            ],
            Guess::VERY_HIGH_CONFIDENCE
        );

        self::assertEquals($typeGuess, $enumTypeGuesser->guessType(\stdClass::class, 'position'));
    }
}
