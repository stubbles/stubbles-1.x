<?php
/**
 * Test for net::stubbles::service::rest::index::stubRestService.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubRestService');
/**
 * Test for net::stubbles::service::rest::index::stubRestService.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.8.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_index
 */
class stubRestServiceTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestService
     */
    private $restService;
    /**
     * link to be used in test
     *
     * @var  stubRestLink
     */
    private $restLink;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->restLink    = new stubRestLink('self', 'http://example.net/foo');
        $this->restService = new stubRestService($this->restLink,
                                                 'foo service',
                                                 'Foo service description'
                             );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->restService->getClass()->hasAnnotation('XMLTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetLinkMethod()
    {
        $this->assertTrue($this->restService->getClass()
                                            ->getMethod('getLink')
                                            ->hasAnnotation('XMLTag')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetNameMethod()
    {
        $this->assertTrue($this->restService->getClass()
                                            ->getMethod('getName')
                                            ->hasAnnotation('XMLAttribute')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetDescriptionMethod()
    {
        $this->assertTrue($this->restService->getClass()
                                            ->getMethod('getDescription')
                                            ->hasAnnotation('XMLTag')
        );
    }

    /**
     * @test
     */
    public function returnsGivenLink()
    {
        $this->assertSame($this->restLink, $this->restService->getLink());
    }

    /**
     * @test
     */
    public function returnsGivenName()
    {
        $this->assertEquals('foo service', $this->restService->getName());
    }

    /**
     * @test
     */
    public function returnsGivenDescription()
    {
        $this->assertEquals('Foo service description',
                            $this->restService->getDescription()
        );
    }
}
?>