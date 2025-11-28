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
use Fresh\DoctrineEnumBundle\Exception\UnexpectedValueException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * UnexpectedValueExceptionTest.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class UnexpectedValueExceptionTest extends TestCase
{
    #[Test]
    public function validCreation(): void
    {
        $exception = new UnexpectedValueException();

        $this->assertInstanceOf(ExceptionInterface::class, $exception);
        $this->assertInstanceOf(\UnexpectedValueException::class, $exception);
    }
}
