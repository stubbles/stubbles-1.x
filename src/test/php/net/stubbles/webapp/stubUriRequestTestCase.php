<?php
/**
 * Test for net::stubbles::webapp::stubUriRequest.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::stubUriRequest');
/**
 * Test for net::stubbles::webapp::stubUriRequest.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 */
class stubUriRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function alwaysSatisfiesNullCondition()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertTrue($uriRequest->satisfies(null));
    }

    /**
     * @test
     */
    public function alwaysSatisfiesEmptyCondition()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertTrue($uriRequest->satisfies(''));
    }

    /**
     * @test
     */
    public function returnsTrueForSatisfiedCondition()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertTrue($uriRequest->satisfies('^/xml/'));
    }

    /**
     * @test
     */
    public function returnsFalseForNonSatisfiedCondition()
    {
        $uriRequest = new stubUriRequest('/rss/articles');
        $this->assertFalse($uriRequest->satisfies('^/xml/'));
    }

    /**
     * @test
     */
    public function getProcessorUriReturnsSlashOnlyWhenNoProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertEquals('/',
                            $uriRequest->getProcessorUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsEverythingExceptSlashOnlyWhenNoProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/Home');
        $this->assertEquals('Home',
                            $uriRequest->getRemainingUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsEverythingIncludingDotsExceptSlashOnlyWhenNoProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/Home.html');
        $this->assertEquals('Home.html',
                            $uriRequest->getRemainingUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsEverythingExceptParametersOnlyWhenNoProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/Home?foo=bar');
        $this->assertEquals('Home',
                            $uriRequest->getRemainingUri()
        );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsEmptyStringOnlyWhenNoProcessorUriConditionSetAndNoRemainingPartLeft()
    {
        $uriRequest = new stubUriRequest('/');
        $this->assertEquals('',
                            $uriRequest->getRemainingUri()
        );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsEmptyStringOnlyWhenNoProcessorUriConditionSetAndOnlyParametersLeft()
    {
        $uriRequest = new stubUriRequest('/?foo=bar');
        $this->assertEquals('',
                            $uriRequest->getRemainingUri()
        );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsFallbackOnlyWhenNoProcessorUriConditionSetAndNoRemainingPartLeft()
    {
        $uriRequest = new stubUriRequest('/');
        $this->assertEquals('index',
                            $uriRequest->getRemainingUri('index')
        );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsFallbackOnlyWhenNoProcessorUriConditionSetAndOnlyParametersLeft()
    {
        $uriRequest = new stubUriRequest('/?foo=bar');
        $this->assertEquals('index',
                            $uriRequest->getRemainingUri('index')
        );
    }

    /**
     * @test
     */
    public function getProcessorUriReturnsEmptyStringOnNonMatch()
    {
        $uriRequest = new stubUriRequest('/other/Home');
        $this->assertEquals('',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getProcessorUri()
        );
    }

    /**
     * @test
     */
    public function getProcessorUriReturnsProcessorPartWhenProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertEquals('/xml/',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getProcessorUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsNonProcessorPartWhenProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/xml/Home');
        $this->assertEquals('Home',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getRemainingUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsNonProcessorPartWithoutParametersWhenProcessorUriConditionSet()
    {
        $uriRequest = new stubUriRequest('/xml/Home?foo=bar');
        $this->assertEquals('Home',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getRemainingUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsEmptyStringWhenProcessorUriConditionSetButUriDoesNotContainMore()
    {
        $uriRequest = new stubUriRequest('/xml/');
        $this->assertEquals('',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getRemainingUri()
        );
    }

    /**
     * @test
     */
    public function getRemainingUriReturnsEmptyStringWhenProcessorUriConditionSetButUriDoesContainOnlyParameters()
    {
        $uriRequest = new stubUriRequest('/xml/?foo=bar');
        $this->assertEquals('',
                            $uriRequest->setProcessorUriCondition('^/xml/')
                                       ->getRemainingUri()
        );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsFallbackWhenProcessorUriConditionSetButUriDoesNotContainMore()
    {
        $uriRequest = new stubUriRequest('/xml/');
        $this->assertEquals('index',
                             $uriRequest->setProcessorUriCondition('^/xml/')
                                        ->getRemainingUri('index')
         );
    }

    /**
      * @test
      */
    public function getRemainingUriReturnsFallbackWhenProcessorUriConditionSetButUriDoesContainOnlyParameters()
    {
        $uriRequest = new stubUriRequest('/xml/?foo=bar');
        $this->assertEquals('index',
                             $uriRequest->setProcessorUriCondition('^/xml/')
                                        ->getRemainingUri('index')
         );
    }
}
?>