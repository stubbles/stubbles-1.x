<?php
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgent.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @version     $Id: stubUserAgentTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::useragent::stubUserAgent');
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgent.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @since       1.2.0
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_useragent
 */
class stubUserAgentTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubUserAgent
     */
    protected $userAgent;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->userAgent = new stubUserAgent('name', false);
    }

    /**
     * @test
     */
    public function iocAnnotationsPresent()
    {
        $this->assertTrue($this->userAgent->getClass()->hasAnnotation('ProvidedBy'));
    }

    /**
     * @test
     */
    public function xmlAnnotationsPresent()
    {
        $class = $this->userAgent->getClass();
        $this->assertTrue($class->hasAnnotation('XMLTag'));
        $this->assertTrue($class->getMethod('getName')->hasAnnotation('XMLAttribute'));
        $this->assertTrue($class->getMethod('isBot')->hasAnnotation('XMLAttribute'));
    }

    /**
     * @test
     */
    public function instanceReturnsGivenValues()
    {
        $this->assertEquals('name', $this->userAgent->getName());
        $this->assertFalse($this->userAgent->isBot());
    }
}
?>