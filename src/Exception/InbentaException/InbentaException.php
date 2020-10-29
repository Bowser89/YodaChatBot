<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
 */

declare(strict_types=1);

namespace App\Exception\InbentaException;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Exception used to maps exceptions from backend web service.
 */
class InbentaException extends HttpException
{
    public const ERR_MSG_INTERNAL_ERROR = 'Internal Error';
}
