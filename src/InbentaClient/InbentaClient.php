<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\InbentaClient;

use App\Entity\AuthenticationToken;
use App\Entity\SessionToken;
use App\Exception\InbentaException\InbentaException;
use App\Service\AuthenticationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * InbentaClient.
 *
 * Client for the Inbenta endpoints with token management.
 */
class InbentaClient implements InbentaClientInterface
{
    /**
     * @var HttpClientInterface The http client
     */
    private $client;

    /**
     * @var SessionInterface The user session
     */
    private $session;

    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * The logger.
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * The Inbenta api key.
     *
     * @var string
     */
    private $inbentaApiKey;

    /**
     * The Inbenta api version.
     *
     * @var string
     */
    private $inbentaApiVersion;

    /**
     * The constructor method.
     */
    public function __construct(
        HttpClientInterface $client,
        SessionInterface $session,
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        string $inbentaApiKey,
        string $inbentaApiVersion
    ) {
        $this->client                = $client;
        $this->session               = $session;
        $this->authenticationService = $authenticationService;
        $this->logger                = $logger;
        $this->inbentaApiKey         = $inbentaApiKey;
        $this->inbentaApiVersion     = $inbentaApiVersion;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function call(array $request): ResponseInterface
    {
        try {
            $authenticationToken = $this->authenticationService->setAndReturnAuthenticationTokenIfNotExists();
            $sessionToken        = $this->authenticationService->checkAndReturnSessionToken();
            $baseRequest         = $this->createBaseRequest($authenticationToken, $sessionToken);
            $url                 = sprintf('%s/%s%s', $authenticationToken->getChatBotApiUrl(), $this->inbentaApiVersion, $request['endPoint']);
            $completeRequest     = $this->createCompleteRequest($baseRequest, $request);

            return $this->client->request(
                $request['method'],
                $url,
                $completeRequest
            );
        } catch (InbentaException $e) {
            throw new InbentaException($e->getCode(), $e->getMessage());
        } catch (TransportExceptionInterface $e) {
            throw new InbentaException(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }
    }

    /**
     * Adds body to the request if set.
     */
    private function createCompleteRequest(array $request, array $additionalRequestValues): array
    {
        if (array_key_exists('body', $additionalRequestValues)) {
            $request['body'] = json_encode($additionalRequestValues['body']);
        }

        return $request;
    }

    /**
     * Returns a base request array.
     */
    private function createBaseRequest(AuthenticationToken $authenticationToken, SessionToken $sessionToken): array
    {
        return [
                'headers' => [
                    'Content-Type'                                      => 'application/json',
                    'Authorization'                                     => sprintf('Bearer %s', $authenticationToken->getAccessToken()),
                    AuthenticationService::X_INBENTA_API_KEY_HEADER     => $this->inbentaApiKey,
                    AuthenticationService::X_INBENTA_SESSION_KEY_HEADER => sprintf('Bearer %s', $sessionToken->getSessionToken()),
                ],
            ];
    }
}
