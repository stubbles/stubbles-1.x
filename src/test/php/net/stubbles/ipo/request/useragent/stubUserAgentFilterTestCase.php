<?php
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgentFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @version     $Id: stubUserAgentFilterTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::useragent::stubUserAgentFilter');
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgentFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @since       1.2.0
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_useragent
 */
class stubUserAgentFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubUserAgentFilter
     */
    protected $userAgentFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->userAgentFilter = new stubUserAgentFilter(new stubUserAgentDetector());
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->userAgentFilter->getClass()->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function executeReturnsUserAgent()
    {
        $this->assertInstanceOf('stubUserAgent', $this->userAgentFilter->execute('a user agent'));
    }
}
?>