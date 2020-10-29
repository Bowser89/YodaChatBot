<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\AuthenticationToken;
use App\Entity\SessionToken;
use App\Exception\InbentaException\InvalidInbentaParametersException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * AuthenticationService.
 */
class AuthenticationService
{
    /**
     * Inbenta headers keys.
     */
    const X_INBENTA_API_KEY_HEADER     = 'x-inbenta-key';
    const X_INBENTA_SESSION_KEY_HEADER = 'x-inbenta-session';

    /**
     * Custom session keys.
     */
    const AUTHENTICATION_TOKEN_SESSION_KEY = 'authenticationToken';
    const SESSION_TOKEN_KEY                = 'sessionToken';

    /**
     * Authentication and session endpoints.
     */
    const INBENTA_AUTHENTICATION_ENDPOINT               = '/auth';
    const INBENTA_AUTHENTICATION_REFRESH_TOKEN_ENDPOINT = '/refreshToken';
    const INBENTA_CHATBOT_OPEN_CONVERSATION_ENDPOINT    = '/conversation';

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
     * The constructor method.
     */
    public function __construct(
        HttpClientInterface $client,
        SessionInterface $session,
        string $inbentaApiKey,
        string $inbentaSecretKey,
        string $inbentaAuthenticationUri,
        string $inbentaApiVersion
    ) {
        $this->client                   = $client;
        $this->session                  = $session;
        $this->inbentaApiKey            = $inbentaApiKey;
        $this->inbentaSecretKey         = $inbentaSecretKey;
        $this->inbentaAuthenticationUri = $inbentaAuthenticationUri;
        $this->inbentaApiVersion        = $inbentaApiVersion;
    }

    /**
     * Gets and stores in session an autentication token if not already set.
     * If token is almost expired it's automatically refreshed.
     */
    public function setAuthenticationTokenIfNotExists(): void
    {
        /** @var AuthenticationToken|null $authenticationToken */
        $authenticationToken = $this->session->get(self::AUTHENTICATION_TOKEN_SESSION_KEY);
        $baseRequest         = $this->createAuthenticationBaseRequest();

        if (!$authenticationToken) {
            $this->createAndStoreAuthenticationToken($baseRequest);
        } else {
            if ($authenticationToken->hasToBeRefreshed()) {
                $this->refreshAuthenticationToken($authenticationToken, $baseRequest);
            } elseif ($authenticationToken->isExpired()) {
                $this->session->clear();
                $this->createAndStoreAuthenticationToken($baseRequest);
            }
        }
    }

    /**
     * Refreshes the authentication token.
     */
    public function refreshAuthenticationToken(AuthenticationToken $authenticationToken, array $request): void
    {
        $request['headers']['Authorization'] = sprintf('Bearer %s', $authenticationToken->getAccessToken());
        $authenticationUrl                   = $this->generateAuthenticationBaseUrl();

        $response = $this->client->request(
            'POST',
            sprintf('%s%s', $authenticationUrl, self::INBENTA_AUTHENTICATION_REFRESH_TOKEN_ENDPOINT),
            $request
        );

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new InvalidInbentaParametersException($response->getStatusCode(), 'There was a problem during the authentication process. Please check the authentication variables.');
        }
        $decodedResponse = json_decode($response->getContent(), true);
        $authenticationToken
            ->setAccessToken($decodedResponse['accessToken'])
            ->setTokenExpiration($decodedResponse['expiration'])
            ->setTokenExpirationRemainingTime($decodedResponse['expires_in']);
        $this->session->set(self::AUTHENTICATION_TOKEN_SESSION_KEY, $authenticationToken);
    }

    /**
     * Sets a new session token if not defined already.
     */
    public function checkSessionToken(): void
    {
        if (!$this->session->get(self::SESSION_TOKEN_KEY)) {
            /** @var AuthenticationToken $authenticationToken */
            $authenticationToken                 = $this->session->get(self::AUTHENTICATION_TOKEN_SESSION_KEY);
            $chatbotUrl                          = $authenticationToken->getChatBotApiUrl();
            $request                             = $this->createAuthenticationBaseRequest();
            $request['headers']['Authorization'] = sprintf('Bearer %s', $authenticationToken->getAccessToken());
            $response                            = $this->client->request(
                'POST',
                sprintf('%s/%s%s', $chatbotUrl, $this->inbentaApiVersion, self::INBENTA_CHATBOT_OPEN_CONVERSATION_ENDPOINT),
                $request
            );

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                throw new InvalidInbentaParametersException($response->getStatusCode(), 'There was a problem during the authentication process. Please check the authentication variables.');
            }
            $decodedResponse = json_decode($response->getContent(), true);
            $sessionId       = $decodedResponse['sessionId'];
            $sessionToken    = $decodedResponse['sessionToken'];
            $sessionToken    = new SessionToken($sessionId, $sessionToken);

            $this->session->set(self::SESSION_TOKEN_KEY, $sessionToken);
        }
    }

    /**
     * Creates and returns an authentication base request.
     */
    public function createAuthenticationBaseRequest(): array
    {
        return [
                'headers' => [
                    'Content-Type'                 => 'application/json',
                    self::X_INBENTA_API_KEY_HEADER => $this->inbentaApiKey,
                ],
            ];
    }

    /**
     * Creates and stores in session an authentication token.
     */
    private function createAndStoreAuthenticationToken(array $request): void
    {
        $authenticationBaseUrl = $this->generateAuthenticationBaseUrl();
        $request['body']       = json_encode(['secret' => $this->inbentaSecretKey]);
        $response              = $this->client->request(
            'POST',
            sprintf('%s%s', $authenticationBaseUrl, self::INBENTA_AUTHENTICATION_ENDPOINT),
            $request
        );

        if (Response::HTTP_OK !== $response->getStatusCode()) {
            throw new InvalidInbentaParametersException($response->getStatusCode(), 'There was a problem during the authentication process. Please check the authentication variables.');
        }
        $decodedResponse     = json_decode($response->getContent(), true);
        $authenticationToken = new AuthenticationToken(
                $decodedResponse['accessToken'],
                $decodedResponse['expiration'],
                $decodedResponse['expires_in'],
                $decodedResponse['apis'],
                $decodedResponse['isUserIdentified']
            );

        $this->session->set(self::AUTHENTICATION_TOKEN_SESSION_KEY, $authenticationToken);
    }

    /**
     * Builds the authentication base url.
     */
    private function generateAuthenticationBaseUrl(): string
    {
        return sprintf('%s%s', $this->inbentaAuthenticationUri, $this->inbentaApiVersion);
    }
}
