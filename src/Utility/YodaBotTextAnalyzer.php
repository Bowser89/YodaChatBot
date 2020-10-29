<?php

declare(strict_types=1);

namespace App\Utility;

/**
 * YodaBotTextAnalyzer.
 */
class YodaBotTextAnalyzer implements TextAnalyzerInterface
{
    /**
     * The word to search in the message.
     */
    private const WORD_TO_SEARCH = 'force';

    /**
     * Checks if the given text has the word "force" in it.
     */
    public function analyze(string $message): bool
    {
        $lowercaseMessage = strtolower($message);
        if (strpos($lowercaseMessage, self::WORD_TO_SEARCH) !== false) {
            return true;
        }

        return false;
    }
}