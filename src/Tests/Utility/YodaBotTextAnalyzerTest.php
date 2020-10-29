<?php
/**
 * This file is part of the eLearnSecurity website project.
 *
 * Created on 29/10/2020
 *
 * @copyright Caendra Inc.
 */

namespace App\Test\Utility;

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