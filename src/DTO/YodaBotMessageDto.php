<?php

namespace App\DTO;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * YodaBotMessageDto.
 */
class YodaBotMessageDto
{
    /**
     * Constants to identify whose sent a message.
     */
    const YODABOT_SOURCE = 'YodaBot';
    const HUMAN_SOURCE   = 'Human';
    /**
     * The message.
     *
     * @var array
     */
    private $message;

    /**
     * The message source.
     *
     * @var string
     */
    private $source;

    /**
     * The title phrase in case the message is received from graph api call.
     *
     * @var string|null
     */
    private $titlePhrase = null;

    /**
     * Returns a formatted object that will be sent to the frontend.
     */
    public static function createFormattedMessage(array $message, string $source, string $titlePhrase = null): self
    {
        $yodaBotDto = new self();

        if ($titlePhrase) {
            $yodaBotDto->setTitlePhrase($titlePhrase);
        }

        $yodaBotDto
            ->setMessage($message)
            ->setSource($source);

        return $yodaBotDto;
    }

    /**
     * Gets the message.
     */
    public function getMessage(): array
    {
        return $this->message;
    }

    /**
     * @param array $message
     */
    public function setMessage(array $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Gets the source.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Sets the source.
     */
    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Gets the source.
     */
    public function getTitlePhrase(): ?string
    {
        return $this->titlePhrase;
    }

    /**
     * Sets the source.
     */
    public function setTitlePhrase(string $titlePhrase): self
    {
        $this->titlePhrase = $titlePhrase;

        return $this;
    }
}