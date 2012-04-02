<?php
/**
 * Test for net::stubbles::service::rest::index::stubRestServices.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubRestServices');
/**
 * Test for net::stubbles::service::rest::index::stubRestServices.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.8.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_index
 */
class stubRestServicesTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestServices
     */
    private $restServices;
    /**
     * service to be used in test
     *
     * @var  stubRestService
     */
    private $restService;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->restService  = new stubRestService(new stubRestLink('self', 'http://example.net/foo'),
                                                  'foo service',
                                                  'Foo service description'
                              );
        $this->restServices = new stubRestServices();
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->restServices->getClass()->hasAnnotation('XMLTag'));
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetEnvironmentMethod()
    {
        $this->assertTrue($this->restServices->getClass()
                                             ->getMethod('getEnvironment')
                                             ->hasAnnotation('XMLTag')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnGetServicesnMethod()
    {
        $this->assertTrue($this->restServices->getClass()
                                            ->getMethod('getServices')
                                            ->hasAnnotation('XMLTag')
        );
    }

    /**
     * @test
     */
    public function hasNoEnvironmentByDefault()
    {
        $this->assertEquals(array('name' => 'n/a'),
                            $this->restServices->getEnvironment()
        );
    }

    /**
     * @test
     */
    public function returnsGivenEnvironment()
    {
        $this->assertEquals(array('name' => 'foo'),
                            $this->restServices->setEnvironmentName('foo')
                                               ->getEnvironment()
        );
    }

    /**
     * @test
     */
    public function hasNoServicesByDefault()
    {
        $this->assertEquals(array(),
                            $this->restServices->getServices()
        );
    }

    /**
     * @test
     */
    public function returnsAddedService()
    {
        $this->assertSame($this->restService,
                            $this->restServices->addService($this->restService)
        );
    }

    /**
     * @test
     */
    public function returnsListOfAddedServices()
    {
        $this->restServices->addService($this->restService);
        $this->assertEquals(array($this->restService),
                            $this->restServices->getServices()
        );
    }
}
?>