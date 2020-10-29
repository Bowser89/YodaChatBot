<?php

/**
 * This file is part of the eLearnSecurity website project.
 *
 * @copyright Caendra Inc.
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
