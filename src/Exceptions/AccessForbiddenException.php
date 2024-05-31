<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class AccessForbiddenException extends HttpException
{
    public const ERROR_MESSAGE = 'Direct access to this route is not permitted.';

    public function __construct(
        string $message = self::ERROR_MESSAGE,
        int $statusCode = Response::HTTP_FORBIDDEN,
        ?Throwable $previous = null,
        array $headers = []
    ) {
        parent::__construct($statusCode, $message, $previous, $headers);
    }
}
