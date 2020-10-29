<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * The response exception used for AJAX requests.
 */
class ResponseException extends HttpException
{
    /**
     * The error content.
     *
     * @var array
     */
    protected $content;

    /**
     * The constructor method.
     */
    public function __construct(string $message, int $errno, array $content = [], int $statusCode = 400)
    {
        parent::__construct($statusCode, $message, null, [], $errno);

        $this->content = $content;
    }

    /**
     * Returns the possible content of exception.
     */
    public function getContent(): array
    {
        return $this->content;
    }
}
