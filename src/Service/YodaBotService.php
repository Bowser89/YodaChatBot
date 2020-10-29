<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\YodaBotMessageDto;
use App\YodaBotClient\YodaBotSendMessageClient;

/**
 * YodaBotService.
 */
class YodaBotService
{
    /**
     * Utilities values.
     */
    const SESSION_NOT_FOUND_MESSAGE_KEY = 'answerNotFound';
    const ANSWER_NOT_FOUND_FLAG_FIELD   = 'no-results';
    const NOT_FOUND_ANSWERS_THRESHOLD   = 2;
    /**
     * The YodaBotSendMessageClient instance.
     *
     * @var YodaBotSendMessageClient
     */
    private $yodaBotSendMessageClient;

    public function __construct(YodaBotSendMessageClient $yodaBotSendMessageClient)
    {
        $this->yodaBotSendMessageClient = $yodaBotSendMessageClient;
    }

    /**
     * Sends a message to YodaBot and returns a DTO.
     */
    public function sendMessage(string $message): YodaBotMessageDto
    {
        return $this->yodaBotSendMessageClient->sendMessage($message);
    }
}