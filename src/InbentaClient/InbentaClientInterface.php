<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
 */

namespace App\InbentaClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface InbentaClientInterface
{
    /**
     * Executes the HTTP requests to Inbenta chatbot taking care of the token management.
     */
    public function call(array $request): ResponseInterface;
}
