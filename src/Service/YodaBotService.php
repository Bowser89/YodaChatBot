<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\YodaBotMessageDto;
use App\YodaBotClient\YodaBotSendMessageClient;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * YodaBotService.
 */
class YodaBotService
{
    /**
     * Utilities constants.
     */
    const SESSION_CONVERSATION_LIST = 'conversationList';

    /**
     * The YodaBotSendMessageClient instance.
     *
     * @var YodaBotSendMessageClient
     */
    private $yodaBotSendMessageClient;

    /**
     * The session.
     *
     * @var SessionInterface
     */
    private $session;

    public function __construct(YodaBotSendMessageClient $yodaBotSendMessageClient, SessionInterface $session)
    {
        $this->yodaBotSendMessageClient = $yodaBotSendMessageClient;
        $this->session                  = $session;
    }

    /**
     * Sends a message to YodaBot and returns a DTO.
     */
    public function sendMessage(string $message): YodaBotMessageDto
    {
        return $this->yodaBotSendMessageClient->sendMessage($message);
    }

    /**
     * Stores in session a serialized message DTO.
     */
    public function saveMessageInSession(YodaBotMessageDto $formattedMessage): void
    {
        $previousConversation   = $this->session->get(self::SESSION_CONVERSATION_LIST);
        $previousConversation[] = $formattedMessage->serialize();

        $this->session->replace([self::SESSION_CONVERSATION_LIST => $previousConversation]);
    }
}