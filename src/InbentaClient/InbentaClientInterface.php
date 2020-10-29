<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\InbentaClient;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface InbentaClientInterface
{
    /**
     * Executes the HTTP requests to Inbenta chatbot taking care of the token management.
     */
    public function call(array $request): ResponseInterface;
}
