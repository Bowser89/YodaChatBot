<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * AuthenticationToken.
 */
class AuthenticationToken
{
    /**
     * Number of seconds threshold where the token has to be refreshed.
     */
    const TIMESTAMP_REFRESH_THRESHOLD = 180;

    /**
     * The array key where the chatbot api url is associated.
     */
    const CHATBOT_API_KEY = 'chatbot';

    /**
     * The access token value.
     *
     * @var string
     */
    private $accessToken;

    /**
     * The timestamp date until the token is valid.
     *
     * @var int
     */
    private $tokenExpiration;

    /**
     * The token TTL in seconds.
     *
     * @var int
     */
    private $tokenExpirationRemainingTime;

    /**
     * An array containing API useful urls.
     *
     * @var array
     */
    private $apiInformation;

    /**
     * Check if user is identified.
     *
     * @var bool
     */
    private $isUserIdentified;

    public function __construct(
        string $accessToken,
        int $tokenExpiration,
        int $tokenExpirationRemainingTime,
        array $apiInformation,
        bool $isUserIdentified
    ) {
        $this->accessToken                  = $accessToken;
        $this->tokenExpiration              = $tokenExpiration;
        $this->tokenExpirationRemainingTime = $tokenExpirationRemainingTime;
        $this->apiInformation               = $apiInformation;
        $this->isUserIdentified             = $isUserIdentified;
    }

    /**
     * Gets the access token value.
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Gets the timestamp date until the token is valid.
     */
    public function getTokenExpiration(): int
    {
        return $this->tokenExpiration;
    }

    /**
     * Gets the token expiration.
     */
    public function getTokenExpirationRemainingTime(): int
    {
        return $this->tokenExpirationRemainingTime;
    }

    /**
     * Gets the API.
     */
    public function getApiInformation(): array
    {
        return $this->apiInformation;
    }

    /**
     * Sets the access token value.
     */
    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Sets the timestamp date until the token is valid.
     */
    public function setTokenExpiration(int $tokenExpiration): self
    {
        $this->tokenExpiration = $tokenExpiration;

        return $this;
    }

    /**
     * Sets the token expiration.
     */
    public function setTokenExpirationRemainingTime(int $tokenExpirationRemainingTime): self
    {
        $this->tokenExpirationRemainingTime = $tokenExpirationRemainingTime;

        return $this;
    }

    /**
     * Checks if user is identified.
     */
    public function isUserIdentified(): bool
    {
        return $this->isUserIdentified;
    }

    /**
     * Checks if the token has to be refreshed.
     */
    public function hasToBeRefreshed(): bool
    {
        $timeStampDifference = $this->tokenExpiration - time();

        return 0 < $timeStampDifference && self::TIMESTAMP_REFRESH_THRESHOLD > $timeStampDifference;
    }

    /**
     * Checks if token is expired.
     */
    public function isExpired(): bool
    {
        $timeStampDifference = $this->tokenExpiration - time();

        return 0 > $timeStampDifference;
    }

    /**
     * Gets the chatbot api url.
     */
    public function getChatBotApiUrl(): string
    {
        return $this->apiInformation[self::CHATBOT_API_KEY];
    }
}
