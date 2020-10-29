<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
 */

namespace App\Exception;

/**
 * The exception raised when a parameter is invalid.
 */
class InvalidParameterException extends ResponseException
{
    /**
     * Constructor method.
     *
     * @param string $message the message
     * @param array  $content the content
     */
    public function __construct($message, array $content = [])
    {
        parent::__construct($message, 0003, $content);
    }
}
