<?php

namespace App\Controller;

use App\Service\YodaBotService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * YodaBotController.
 */
class YodaBotController extends AbstractController
{
    /**
     * Action for route: yoda_api.send_message.
     */
    public function sendMessageAction(Request $request, YodaBotService $yodaBotService): JsonResponse
    {
        $decodedContent = json_decode($request->getContent(), true);
        $response       = $yodaBotService->sendMessage($decodedContent['message']);

        return new JsonResponse([
            'titlePhrase' => $response->getTitlePhrase(),
            'messages'    => $response->getMessage(),
            'source'      => $response->getSource(),
        ], 200);
    }
}