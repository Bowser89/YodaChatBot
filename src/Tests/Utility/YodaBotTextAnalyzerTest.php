<?php

namespace App\Tests\Utility;

use App\Utility\YodaBotTextAnalyzer;
use PHPUnit\Framework\TestCase;

/**
 * YodaBotTextAnalyzerTest.
 * 
 * @covers \App\Utility\YodaBotTextAnalyzer
 */
class YodaBotTextAnalyzerTest extends TestCase
{
    /**
     * The Yodabot text analyzer instance.
     * 
     * @var YodaBotTextAnalyzer
     */
    private $yodaBotTextAnalyzer;
    
    protected function setUp()
    {
        parent::setUp();
        
        $this->yodaBotTextAnalyzer = new YodaBotTextAnalyzer();
    }

    /**
     * @covers ::analyze
     */
    public function testAnalyzerReturnsTrueIfWordIsFoundInMessage(): void 
    {
        $message = 'This message contains the word force';
        
        $wordForceIsFound = $this->yodaBotTextAnalyzer->analyze($message);
        
        $this->assertTrue($wordForceIsFound);
    }
}