<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
 */

namespace App\Exception\InbentaException;

use App\Exception\InvalidParameterException;

/**
 * The exception raised when user sends an invalid token.
 */
class InvalidTokenException extends InvalidParameterException
{
}
