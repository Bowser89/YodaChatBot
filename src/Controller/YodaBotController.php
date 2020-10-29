<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

namespace App\Controller;

use App\DTO\YodaBotMessageDto;
use App\Service\YodaBotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * YodaBotController.
 */
class YodaBotController extends AbstractController
{
    /**
     * The response array key where the user's message is stored.
     */
    const USER_SENT_MESSAGE_KEY = 'message';

    /**
     * Action for route: yoda_api.send_message.
     */
    public function sendMessageAction(Request $request, YodaBotService $yodaBotService, SessionInterface $session): JsonResponse
    {
        $decodedContent = json_decode($request->getContent(), true);
        $userMessage    = $decodedContent[self::USER_SENT_MESSAGE_KEY];

        // Saving message in session
        $userMessageDto = YodaBotMessageDto::createFormattedMessage([$userMessage], YodaBotMessageDto::HUMAN_SOURCE);
        $yodaBotService->saveMessageInSession($userMessageDto);

        // Sending message to Yodabot
        $response = $yodaBotService->sendMessage($userMessage);
        $yodaBotService->saveMessageInSession($response);

        return new JsonResponse([
            'message' => $response->serialize(),
        ], Response::HTTP_OK);
    }
}
