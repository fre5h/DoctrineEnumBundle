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
use Fresh\DoctrineEnumBundle\Exception\LogicException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * LogicExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class LogicExceptionTest extends TestCase
{
    #[Test]
    public function validCreation(): void
    {
        $exception = new LogicException();

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertInstanceOf(\LogicException::class, $exception);
    }
}
