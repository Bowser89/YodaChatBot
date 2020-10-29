<?php

/**
 * This file is part of the Inbenta coding challenge.
 */

declare(strict_types=1);

namespace App\Utility;

/**
 * TextAnalyzerInterface.
 */
interface TextAnalyzerInterface
{
    public function analyze(string $message): bool;
}
