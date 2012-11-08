<?php
/*
 * This file is part of the FreshDoctrineEnumBundle
 *
 * (c) Artem Genvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!class_exists('Composer\\Autoload\\ClassLoader', false)) {
    $loader = require __DIR__.'/../vendor/autoload.php';
} else {
    $loader = new Composer\Autoload\ClassLoader();
    $loader->register();
}