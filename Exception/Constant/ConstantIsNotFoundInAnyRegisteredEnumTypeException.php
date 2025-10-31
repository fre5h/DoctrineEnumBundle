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

namespace Fresh\DoctrineEnumBundle\Exception\Constant;

use Fresh\DoctrineEnumBundle\Exception\LogicException;

/**
 * ConstantIsNotFoundInAnyRegisteredEnumTypeException.
 *
 * @author Artem Henvald <genvaldartem@gmail.com>
 */
class ConstantIsNotFoundInAnyRegisteredEnumTypeException extends LogicException
{
}
