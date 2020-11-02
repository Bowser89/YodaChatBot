<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\YodaBotClient;

use App\DTO\YodaBotMessageDto;

/**
 * YodaBotSendMessageClient.
 */
class YodaBotSendMessageClient extends YodaBotAbstractClient
{
    /**
     * Utilities constants.
     */
    const SESSION_NOT_FOUND_MESSAGE_KEY          = 'answerNotFound';
    private const SEND_MESSAGE_ENDPOINT_KEY      = 'endPoint';
    private const SEND_MESSAGE_ENDPOINT_VALUE    = '/conversation/message';
    private const SEND_MESSAGE_HTTP_METHOD_KEY   = 'method';
    private const SEND_MESSAGE_HTTP_METHOD_VALUE = 'POST';
    private const SEND_MESSAGE_BODY_KEY          = 'body';
    private const ANSWER_NOT_FOUND_FLAG_FIELD    = 'no-results';
    private const NOT_FOUND_ANSWERS_THRESHOLD    = 2;
    private const NOT_FOUND_MESSAGE              = 'Sorry, I haven\'t found an answer to your question! Please try again.';

    /**
     * Sends a message to YodaBot.
     *
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function sendMessage(string $message): YodaBotMessageDto
    {
        // Checks if the text contains the word force and returns Star Wars movies list if true
        $analysisResponse = $this->textAnalyzer->analyze($message);

        if ($analysisResponse) {
            $this->resetNotFoundAnswerCounter();

            return $this->returnStarWarsMoviesDto();
        }
        // Default behaviour
        return $this->askYoda($message);
    }

    /**
     * Perform a question to Yoda chatbot and returns the answer. If the user did a question that returns a NOT FOUND
     * twice consequently, it returns a list of Star Wars characters.
     */
    private function askYoda(string $message): YodaBotMessageDto
    {
        $request[self::SEND_MESSAGE_BODY_KEY]        = ['message' => $message];
        $request[self::SEND_MESSAGE_HTTP_METHOD_KEY] = self::SEND_MESSAGE_HTTP_METHOD_VALUE;
        $request[self::SEND_MESSAGE_ENDPOINT_KEY]    = self::SEND_MESSAGE_ENDPOINT_VALUE;

        // Performing request
        $response        = $this->inbentaClient->call($request);
        $decodedResponse = json_decode($response->getContent(), true);
        $answerNotFound  = $this->checkIfNotFoundAndUpdateSessionCounter($decodedResponse);
        $notFoundCounter = $this->session->get(self::SESSION_NOT_FOUND_MESSAGE_KEY);
        $responseDto     = null;

        if (self::NOT_FOUND_ANSWERS_THRESHOLD === $notFoundCounter) {
            $this->resetNotFoundAnswerCounter();
            $responseDto = $this->returnStarWarsCharactersDto();
        } else {
            if ($answerNotFound) {
                $responseDto = $this->returnNotFoundMessage();
            } else {
                $this->resetNotFoundAnswerCounter();
                $responseDto = YodaBotMessageDto::createFormattedMessage($decodedResponse['answers'][0]['messageList'], YodaBotMessageDto::YODABOT_SOURCE);
            }
        }

        return $responseDto;
    }

    /**
     * Returns a DTO with not found message.
     */
    private function returnNotFoundMessage(): YodaBotMessageDto
    {
        return YodaBotMessageDto::createFormattedMessage([self::NOT_FOUND_MESSAGE], YodaBotMessageDto::YODABOT_SOURCE);
    }

    /**
     * Returns a DTO with a list of Star Wars movies.
     */
    private function returnStarWarsMoviesDto(): YodaBotMessageDto
    {
        $starWarsMovies = $this->inbentaGraphApiClient->getStarWarsMovies();

        return YodaBotMessageDto::createFormattedMessage(
            $starWarsMovies,
            YodaBotMessageDto::YODABOT_SOURCE,
            'The force is in this movies:'
        );
    }

    /**
     * Returns a DTO with a list of Star Wars characters.
     */
    private function returnStarWarsCharactersDto(): YodaBotMessageDto
    {
        $starWarsCharacters = $this->inbentaGraphApiClient->getStarWarsCharacters();

        return YodaBotMessageDto::createFormattedMessage(
            $starWarsCharacters,
            YodaBotMessageDto::YODABOT_SOURCE,
            'Crap! I haven\'t found what you\'re looking for. But hey, here\'s a list of Star Wars Characters:'
        );
    }

    /**
     * Resets the not found answer counter.
     */
    private function resetNotFoundAnswerCounter(): void
    {
        $this->session->set(self::SESSION_NOT_FOUND_MESSAGE_KEY, 0);
    }

    /**
     * Checks if Yoda hasn't found an answer and increases NOT FOUND answers counter.
     */
    private function checkIfNotFoundAndUpdateSessionCounter(array $response): bool
    {
        $answerFlags     = $response['answers'][0]['flags'];
        $notFoundCounter = $this->session->get(self::SESSION_NOT_FOUND_MESSAGE_KEY);

        if (0 !== count($answerFlags) && self::ANSWER_NOT_FOUND_FLAG_FIELD === $answerFlags[0]) {
            $this->session->set(self::SESSION_NOT_FOUND_MESSAGE_KEY, ++$notFoundCounter);

            return true;
        }

        return false;
    }
}
