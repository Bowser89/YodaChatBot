<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
 */

namespace App\DTO;

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
     * Constants used to serialize the DTO.
     */
    const SERIALIZED_TITLE_PHRASE_FIELD = 'titlePhrase';
    const SERIALIZED_MESSAGES_FIELD     = 'messages';
    const SERIALIZED_SOURCE_FIELD       = 'source';

    /**
     * The messages.
     *
     * @var array
     */
    private $messages;

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
    public static function createFormattedMessage(array $messages, string $source, ?string $titlePhrase = null): self
    {
        $yodaBotDto = new self();

        if ($titlePhrase) {
            $yodaBotDto->setTitlePhrase($titlePhrase);
        }

        $yodaBotDto
            ->setMessages($messages)
            ->setSource($source);

        return $yodaBotDto;
    }

    /**
     * Gets the messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Sets the messages.
     */
    public function setMessages(array $messages): self
    {
        $this->messages = $messages;

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

    /**
     * Serializes the object as array.
     */
    public function serialize(): array
    {
        $encodedDto =
            [
                self::SERIALIZED_MESSAGES_FIELD => $this->getMessages(),
                self::SERIALIZED_SOURCE_FIELD   => $this->getSource(),
            ];

        if ($this->getTitlePhrase()) {
            $encodedDto[self::SERIALIZED_TITLE_PHRASE_FIELD] = $this->getTitlePhrase();
        }

        return $encodedDto;
    }
}
