<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Service\YodaBotService;
use App\YodaBotClient\YodaBotSendMessageClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
        $previousConversation = $session->get(YodaBotService::SESSION_CONVERSATION_LIST) ?? [];

        if (!$previousConversation) {
            $session->set(YodaBotService::SESSION_CONVERSATION_LIST, $previousConversation);
        }

        $data = ['conversation' => $previousConversation];

        return $this->render('index.html.twig', $data);
    }

    /**
     * Action for route: yoda.index.
     */
    public function helloAction(SessionInterface $session): Response
    {

        return $this->render('hello.html.twig');
    }
}
