<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\InbentaGraphApiClient;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * InbentaGraphApiClient.
 */
class InbentaGraphApiClient
{
    /**
     * The client.
     *
     * @var HttpClientInterface
     */
    private $client;

    /**
     * The graph api URL.
     *
     * @var string
     */
    private $graphApiUrl;

    /**
     * The constructor method.
     */
    public function __construct(HttpClientInterface $client, string $inbentaGraphApiUrl)
    {
        $this->client      = $client;
        $this->graphApiUrl = $inbentaGraphApiUrl;
    }

    /**
     * Returns a DTO with all Star Wars movies.
     */
    public function getStarWarsMovies(): array
    {
        $moviesTitles   = [];
        $requestRawData = '{"query":"{allFilms(first: 10) {films {title}}}","variables":{}}';
        $response       = $this->client->request(
            'POST',
            $this->graphApiUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $requestRawData,
            ]
        );

        $retrievedMovies = json_decode($response->getContent());

        foreach ($retrievedMovies->data->allFilms->films as $movie) {
            $moviesTitles[] = $movie->title;
        }

        return $moviesTitles;
    }

    /**
     * Returns an array with all Star Wars characters.
     */
    public function getStarWarsCharacters(): array
    {
        $starWarsCharacters = [];
        $requestRawData     = '{"query":"{allPeople(first: 10) {people {name}}}","variables":{}}';
        $response           = $this->client->request(
            'POST',
            $this->graphApiUrl,
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => $requestRawData,
            ]
        );

        $retrievedCharacters = json_decode($response->getContent());

        foreach ($retrievedCharacters->data->allPeople->people as $character) {
            $starWarsCharacters[] = $character->name;
        }

        return $starWarsCharacters;
    }
}
