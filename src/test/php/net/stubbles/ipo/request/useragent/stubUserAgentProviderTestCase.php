<?php
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgentProvider.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @version     $Id: stubUserAgentProviderTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::useragent::stubUserAgentProvider');
/**
 * Test for net::stubbles::ipo::request::useragent::stubUserAgentProvider.
 *
 * @package     stubbles
 * @subpackage  ipo_request_useragent_test
 * @since       1.2.0
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_useragent
 */
class stubUserAgentProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubUserAgentProvider
     */
    protected $userAgentProvider;
    /**
     * mocked request instance
     *
     * @var
     */
    protected $mockRequest;
    /**
     * filter to retrieve user agent from request
     *
     * @var  stubUserAgentFilter
     */
    protected $userAgentFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest       = $this->getMock('stubRequest');
        $this->userAgentFilter   = new stubUserAgentFilter(new stubUserAgentDetector());
        $this->userAgentProvider = new stubUserAgentProvider($this->mockRequest, $this->userAgentFilter);
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->userAgentProvider->getClass()
                                                  ->getConstructor()
                                                  ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function providerReturnsUserAgent()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->with($this->equalTo('HTTP_USER_AGENT'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'HTTP_USER_AGENT',
                                                                                  'foo'
                                                    )
                                 )
                            );
        $this->assertEquals(new stubUserAgent('foo', false), $this->userAgentProvider->get());
    }
}
?>