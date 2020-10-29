<?php

declare(strict_types=1);

namespace App\YodaBotClient;

use App\InbentaClient\InbentaClient;
use App\InbentaGraphApiClient\InbentaGraphApiClient;
use App\Service\YodaBotService;
use App\Utility\TextAnalyzerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * YodaBotAbstractClient.
 */
abstract class YodaBotAbstractClient
{
    /**
     * The InbentaClient instance.
     *
     * @var InbentaClient
     */
    protected $inbentaClient;

    /**
     * The InbentaGraphApiClient instance.
     *
     * @var InbentaGraphApiClient
     */
    protected $inbentaGraphApiClient;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * A text analyzer instance.
     *
     * @var TextAnalyzerInterface
     */
    protected $textAnalyzer = null;

    /**
     * Constructor method.
     */
    public function __construct
    (InbentaClient $inbentaClient,
     InbentaGraphApiClient $inbentaGraphApiClient,
     SessionInterface $session,
     ?TextAnalyzerInterface $textAnalyzer = null)
    {
        $this->inbentaClient         = $inbentaClient;
        $this->inbentaGraphApiClient = $inbentaGraphApiClient;
        $this->session               = $session;
        $this->textAnalyzer          = $textAnalyzer;
    }
}
