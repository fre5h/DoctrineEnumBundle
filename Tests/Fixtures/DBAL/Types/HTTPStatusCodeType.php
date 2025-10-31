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

namespace Fresh\DoctrineEnumBundle\Tests\Fixtures\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

/**
 * @author Colin O'Dell <colinodell@gmail.com>
 *
 * @extends AbstractEnumType<int, string>
 */
final class HTTPStatusCodeType extends AbstractEnumType
{
    // An incomplete list of HTTP status codes
    public final const HTTP_CONTINUE = 100;
    public final const HTTP_OK = 200;
    public final const HTTP_CREATED = 201;
    public final const HTTP_ACCEPTED = 202;
    public final const HTTP_MOVED_PERMANENTLY = 301;
    public final const HTTP_FOUND = 302;
    public final const HTTP_SEE_OTHER = 303;
    public final const HTTP_NOT_MODIFIED = 304;
    public final const HTTP_BAD_REQUEST = 400;
    public final const HTTP_UNAUTHORIZED = 401;
    public final const HTTP_PAYMENT_REQUIRED = 402;
    public final const HTTP_FORBIDDEN = 403;
    public final const HTTP_NOT_FOUND = 404;
    public final const HTTP_METHOD_NOT_ALLOWED = 405;
    public final const HTTP_INTERNAL_SERVER_ERROR = 500;
    public final const HTTP_NOT_IMPLEMENTED = 501;
    public final const HTTP_BAD_GATEWAY = 502;
    public final const HTTP_SERVICE_UNAVAILABLE = 503;
    public final const HTTP_GATEWAY_TIMEOUT = 504;

    protected string $name = 'HTTPStatusCodeType';

    /**
     * {@inheritdoc}
     */
    protected static array $choices = [
        self::HTTP_CONTINUE  => 'Continue',
        self::HTTP_OK  => 'Ok',
        self::HTTP_CREATED  => 'Created',
        self::HTTP_ACCEPTED  => 'Accepted',
        self::HTTP_MOVED_PERMANENTLY  => 'Moved Permanently',
        self::HTTP_FOUND  => 'Found',
        self::HTTP_SEE_OTHER  => 'See Other',
        self::HTTP_NOT_MODIFIED  => 'Not Modified',
        self::HTTP_BAD_REQUEST  => 'Bad Request',
        self::HTTP_UNAUTHORIZED  => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED  => 'Payment Required',
        self::HTTP_FORBIDDEN  => 'Forbidden',
        self::HTTP_NOT_FOUND  => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED  => 'Method Not Allowed',
        self::HTTP_INTERNAL_SERVER_ERROR  => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED  => 'Not Implemented',
        self::HTTP_BAD_GATEWAY  => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE  => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT  => 'Gateway Timeout',
    ];

    /**
     * {@inheritdoc}
     */
    public static function getDefaultValue(): int
    {
        return self::HTTP_OK;
    }
}
