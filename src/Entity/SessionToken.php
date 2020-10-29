<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Entity;

/**
 * SessionToken.
 */
class SessionToken
{
    /**
     * The session id.
     *
     * @var string
     */
    private $sessionId;

    /**
     * The session token value.
     *
     * @var string
     */
    private $sessionToken;

    /**
     * The constructor method.
     */
    public function __construct(string $sessionId, string $sessionToken)
    {
        $this->sessionId    = $sessionId;
        $this->sessionToken = $sessionToken;
    }

    /**
     * Gets the session token id.
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * Gets the session token.
     */
    public function getSessionToken(): string
    {
        return $this->sessionToken;
    }
}
