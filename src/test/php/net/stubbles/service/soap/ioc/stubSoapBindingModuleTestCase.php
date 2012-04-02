<?php
/**
 * Test for net::stubbles::service::soap::ioc::stubSoapBindingModule.
 *
 * @package     stubbles
 * @subpackage  service_soap_ioc_test
 * @version     $Id: stubSoapBindingModuleTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::ioc::stubSoapBindingModule');
/**
 * Tests for net::stubbles::service::soap::ioc::stubSoapBindingModule.
 *
 * @package     stubbles
 * @subpackage  service_soap_ioc_test
 * @group       service_soap
 */
class stubSoapBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->injector = new stubInjector();
    }

    /**
     * lazy binding should work
     *
     * @test
     */
    public function lazyBinding()
    {
        if (extension_loaded('soap') === false) {
            $this->markTestIncomplete('Test requires extension soap.');
        }
        
        $soapBindingModule = new stubSoapBindingModule();
        $soapBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSoapClientGenerator'));
        $this->assertInstanceOf('stubNativeSoapClient',
                          $this->injector->getInstance('stubSoapClientGenerator')
                                         ->forConfig(new stubSoapClientConfiguration('http://example.net/', 'urn:foo'))
        );
    }

    /**
     * binding with preconfigured clients should work
     *
     * @test
     */
    public function bindingWithPreconfiguredClients()
    {
        $mockSoapClientClass = get_class($this->getMock('stubSoapClient'));
        $soapBindingModule = stubSoapBindingModule::create(array('mock' => $mockSoapClientClass));
        $soapBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSoapClientGenerator'));
        $this->assertInstanceOf($mockSoapClientClass,
                          $this->injector->getInstance('stubSoapClientGenerator')
                                         ->forConfig(new stubSoapClientConfiguration('http://example.net/', 'urn:foo'))
        );
    }
}
?>