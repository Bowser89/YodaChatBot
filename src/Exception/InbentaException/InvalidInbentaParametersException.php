<?php

declare(strict_types=1);

namespace App\Exception\InbentaException;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The exception raised when one or more configuration variables are invalid.
 */
class InvalidInbentaParametersException extends HttpException
{

}