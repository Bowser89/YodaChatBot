<?php

declare(strict_types=1);

namespace App\InbentaClient;

use App\Entity\AuthenticationToken;
use App\Entity\SessionToken;
use App\Exception\InbentaException\InbentaException;
use App\Service\AuthenticationService;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
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
     * Default error status code.
     */
    const DEFAULT_ERROR_STATUS_CODE = 500;

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
     * The inbenta api key
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
     * UaaClient constructor.
     */
    public function __construct
    (
        HttpClientInterface $client,
        SessionInterface $session,
        AuthenticationService $authenticationService,
        LoggerInterface $logger,
        string $inbentaApiKey,
        string $inbentaApiVersion
    )
    {
        $this->client                = $client;
        $this->session               = $session;
        $this->authenticationService = $authenticationService;
        $this->logger                = $logger;
        $this->inbentaApiKey         = $inbentaApiKey;
        $this->inbentaApiVersion     = $inbentaApiVersion;
    }

    /**
     * @param array $request
     * @return ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function call(array $request): ResponseInterface
    {
        try {
            $this->authenticationService->setAuthenticationTokenIfNotExists();
            $this->authenticationService->checkSessionToken();
            $authenticationToken = $this->session->get(AuthenticationService::AUTHENTICATION_TOKEN_SESSION_KEY);
            $sessionToken        = $this->session->get(AuthenticationService::SESSION_TOKEN_KEY);
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
            exit();
            throw new InbentaException(500, $e->getMessage());
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
        $baseRequest  =
            [
                'headers' => [
                    'Content-Type'                                      => 'application/json',
                    'Authorization'                                     => sprintf('Bearer %s', $authenticationToken->getAccessToken()),
                    AuthenticationService::X_INBENTA_API_KEY_HEADER     => $this->inbentaApiKey,
                    AuthenticationService::X_INBENTA_SESSION_KEY_HEADER => sprintf('Bearer %s', $sessionToken->getSessionToken()),
                ],
            ];

        return $baseRequest;
    }
}
