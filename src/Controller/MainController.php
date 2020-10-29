<?php

namespace App\Controller;

use App\InbentaClient\InbentaClient;
use App\InbentaGraphApiClient\InbentaGraphApiClient;
use App\Service\AuthenticationService;
use App\Service\YodaBotService;
use App\YodaBotClient\YodaBotSendMessageClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * MainController.
 */
class MainController extends AbstractController
{
    /**
     * Action for route: yoda.index.
     */
    public function indexAction(SessionInterface $session): Response
    {
        $session->set(YodaBotSendMessageClient::SESSION_NOT_FOUND_MESSAGE_KEY, 0);
        $previousConversation = $session->get(YodaBotService::SESSION_CONVERSATION_LIST);

        if (!$previousConversation) {
            $session->set(YodaBotService::SESSION_CONVERSATION_LIST, []);
        }

        $data = ['conversation' => $previousConversation];

        return $this->render('index.html.twig', $data);
    }
}