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

namespace Fresh\DoctrineEnumBundle\Tests;

use Fresh\DoctrineEnumBundle\DependencyInjection\Compiler\RegisterEnumTypePass;
use Fresh\DoctrineEnumBundle\FreshDoctrineEnumBundle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * FreshDoctrineEnumBundleTest
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
final class FreshDoctrineEnumBundleTest extends TestCase
{
    #[Test]
    public function build(): void
    {
        $containerBuilder = $this->createMock(ContainerBuilder::class);
        $containerBuilder
            ->expects(self::once())
            ->method('addCompilerPass')
            ->with(self::isInstanceOf(RegisterEnumTypePass::class))
        ;

        $bundle = new FreshDoctrineEnumBundle();
        $bundle->build($containerBuilder);
    }
}
