<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Exception\InbentaException;

use App\Exception\InvalidParameterException;

/**
 * The exception raised when user sends an invalid token.
 */
class InvalidTokenException extends InvalidParameterException
{
}
