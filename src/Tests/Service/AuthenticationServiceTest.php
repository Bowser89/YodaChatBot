<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\AuthenticationToken;
use App\Exception\InbentaException\InvalidInbentaParametersException;
use App\Service\AuthenticationService;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * AuthenticationServiceTest.
 *
 * @coversDefaultClass \App\Service\AuthenticationService
 */
class AuthenticationServiceTest extends TestCase
{
    const INBENTA_API_KEY     = 'api key';
    const INBENTA_SECRET_KEY  = 'secret key';
    const INBENTA_AUTH_URI    = 'www.auth.com/uri';
    const INBENTA_API_VERSION = '1';

    /**
     * The http client interface.
     *
     * @var HttpClientInterface|MockObject
     */
    private $client;

    /**
     * The current session.
     *
     * @var SessionInterface|MockArraySessionStorage
     */
    private $session;

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
     * The authentication service instance.
     *
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * The client response object.
     *
     * @var ResponseInterface|MockObject
     */
    private $response;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->inbentaApiKey            = self::INBENTA_API_KEY;
        $this->inbentaSecretKey         = self::INBENTA_SECRET_KEY;
        $this->inbentaAuthenticationUri = self::INBENTA_AUTH_URI;
        $this->inbentaApiVersion        = self::INBENTA_API_VERSION;
        $this->client                   = $this->createMock(HttpClientInterface::class);
        $this->response                 = $this->createMock(ResponseInterface::class);
        $this->session                  = new Session(new MockArraySessionStorage());

        $this->authenticationService = new AuthenticationService(
            $this->client,
            $this->session,
            $this->inbentaApiKey,
            $this->inbentaSecretKey,
            $this->inbentaAuthenticationUri,
            $this->inbentaApiVersion
        );
    }

    /**
     * Tests that the token won't be refreshed if valid and not expired.
     *
     * @covers ::setAndReturnAuthenticationTokenIfNotExists
     */
    public function testSetAuthenticationWontCreateOrRefreshTokenIfValidAndNotExpired(): void
    {
        $tomorrow            = new DateTime('+1 day');
        $authenticationToken = new AuthenticationToken('accessToken', $tomorrow->getTimestamp(), 86400, [], false);

        $this->session->set(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY, $authenticationToken);

        $this->client
            ->expects($this->never())
            ->method('request');

        $this->authenticationService->setAndReturnAuthenticationTokenIfNotExists();

        /** @var AuthenticationToken $authenticationSessionToken */
        $authenticationSessionToken = $this->session->get(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY);

        $this->assertEquals($authenticationToken->getTokenExpiration(), $authenticationSessionToken->getTokenExpiration());
    }

    /**
     * Tests that the token won't be refreshed if valid and not expired.
     *
     * @covers ::setAndReturnAuthenticationTokenIfNotExists
     */
    public function testSetAuthenticationCreatesAndStoresNewTokenIfNotStored(): void
    {
        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($this->createClientAuthenticationTokenResponse());

        $this->authenticationService->setAndReturnAuthenticationTokenIfNotExists();

        /** @var AuthenticationToken $token */
        $token = $this->session->get(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY);

        $this->assertEquals('accessTokenValue', $token->getAccessToken());
        $this->assertEquals(1604056964, $token->getTokenExpiration());
        $this->assertEquals(1200, $token->getTokenExpirationRemainingTime());
        $this->assertEquals('www.auth.uri', $token->getChatBotApiUrl());
        $this->assertFalse($token->isUserIdentified());
    }

    /**
     * Tests that the token is refreshed if valid and expiring.
     *
     * @covers ::setAuthenticationTokenIfNotExists
     */
    public function testSetAuthenticationRefreshesTokenIfExpiring(): void
    {
        // Timestamp representing an almost expired authentication token
        $expiringTimestamp  = time() + 150;
        $almostExpiredToken = new AuthenticationToken('accessTokenValue', $expiringTimestamp, 150, [], false);
        $responseToken      = $this->createClientAuthenticationTokenResponse();

        $this->session->set(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY, $almostExpiredToken);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($responseToken);

        $this->authenticationService->setAndReturnAuthenticationTokenIfNotExists();

        /** @var AuthenticationToken $token */
        $token                = $this->session->get(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY);
        $decodedResponseToken = json_decode($responseToken, true);

        $this->assertEquals('accessTokenValue', $token->getAccessToken());
        $this->assertEquals($decodedResponseToken['expiration'], $token->getTokenExpiration());
        $this->assertEquals(1200, $token->getTokenExpirationRemainingTime());
        $this->assertFalse($token->isUserIdentified());
    }

    /**
     * Tests that the token won't be refreshed if valid and not expired.
     *
     * @covers ::setAndReturnAuthenticationTokenIfNotExists
     */
    public function testSetAuthenticationRaisesExceptionIfClientRequestFails(): void
    {
        $this->expectException(InvalidInbentaParametersException::class);
        $this->expectExceptionMessage('There was a problem during the authentication process. Please check the authentication variables.');

        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);

        $this->response
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->authenticationService->setAndReturnAuthenticationTokenIfNotExists();
    }

    /**
     * Tests that a new session token is properly created and set.
     *
     * @covers ::checkAndReturnSessionToken
     */
    public function testCheckSessionTokenCreatesNewSessionToken(): void
    {
        $apiInformation      = ['chatbot' => 'www.chatbot.url'];
        $authenticationToken = new AuthenticationToken('accessTokenValue', time(), 1200, $apiInformation, false);

        $this->session->set(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY, $authenticationToken);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_OK);

        $this->response
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($this->createClientSessionTokenResponse());

        $sessionTokenReturned = $this->authenticationService->checkAndReturnSessionToken();
        $sessionToken         = $this->session->get(AuthenticationService::SESSION_TOKEN_KEY);

        $this->assertEquals($sessionTokenReturned->getSessionToken(), $sessionToken->getSessionToken());
        $this->assertEquals($sessionTokenReturned->getSessionId(), $sessionToken->getSessionId());
    }

    /**
     * Tests that a new session token is properly created and set.
     *
     * @covers ::checkAndReturnSessionToken
     */
    public function testCheckSessionTokenRaisesExceptionIfClientRequestFails(): void
    {
        $this->expectException(InvalidInbentaParametersException::class);
        $this->expectExceptionMessage('There was a problem during the authentication process. Please check the authentication variables.');

        $apiInformation      = ['chatbot' => 'www.chatbot.url'];
        $authenticationToken = new AuthenticationToken('accessTokenValue', time(), 1200, $apiInformation, false);

        $this->session->set(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY, $authenticationToken);

        $this->client
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->response);

        $this->response
            ->expects($this->exactly(2))
            ->method('getStatusCode')
            ->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);

        $this->authenticationService->checkAndReturnSessionToken();
    }

    /**
     * Returns an encoded array representing the Inbenta client session token response.
     */
    private function createClientSessionTokenResponse(): string
    {
        $responseArray =  [
            'sessionId'    => 'sessionIdValue',
            'sessionToken' => 'sessionTokenValue',
        ];

        return json_encode($responseArray);
    }

    /**
     * Returns an encoded array representing the Inbenta client authentication token response.
     */
    private function createClientAuthenticationTokenResponse(): string
    {
        $responseArray =  [
            'accessToken'      => 'accessTokenValue',
            'expiration'       => 1604056964,
            'expires_in'       => 1200,
            'apis'             => [AuthenticationToken::CHATBOT_API_KEY => 'www.auth.uri'],
            'isUserIdentified' => false,
        ];

        return json_encode($responseArray);
    }
}
