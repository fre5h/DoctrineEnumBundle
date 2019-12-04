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

namespace Fresh\DoctrineEnumBundle\Tests\Exception;

use Fresh\DoctrineEnumBundle\Exception\ExceptionInterface;
use Fresh\DoctrineEnumBundle\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * InvalidArgumentExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class InvalidArgumentExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $exception = new InvalidArgumentException();

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertInstanceOf(\InvalidArgumentException::class, $exception);
    }
}
