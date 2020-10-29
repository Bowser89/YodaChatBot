<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\AuthenticationService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * AuthenticationServiceTest.
 *
 * @coversNothing
 */
class AuthenticationServiceTest extends TestCase
{
    /**
     * The http client interface.
     *
     * @var HttpClientInterface
     */
    private $client;

    /**
     * The Inbenta API key.
     *
     * @var string
     */
    private $inbentaApiKey;

    /**
     * The Inbenta secret key.
     *
     * @var string
     */
    private $inbentaSecretKey;

    /**
     * The Inbenta authentication url.
     *
     * @var string
     */
    private $inbentaAuthenticationUri;

    /**
     * The Inbenta api version.
     *
     * @var string
     */
    private $inbentaApiVersion;

    /**
     * The current session.
     *
     * @var SessionInterface
     */
    private $session;

    /**
     * The authentication service instance.
     *
     * @var AuthenticationService
     */
    private $authenticationService;

    protected function setUp(): void
    {
        parent::setUp();
    }
}
