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
    public const HTTP_CONTINUE = 100;
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;

<<<<<<< HEAD
    protected string $name = 'HTTPStatusCodeType';
=======
    /** @var string */
    protected $name = 'HTTPStatusCodeType';
>>>>>>> 4f0f9ab... Main to 7.x (#203)

    /**
     * {@inheritdoc}
     */
<<<<<<< HEAD
    protected static array $choices = [
=======
    protected static $choices = [
>>>>>>> 4f0f9ab... Main to 7.x (#203)
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
