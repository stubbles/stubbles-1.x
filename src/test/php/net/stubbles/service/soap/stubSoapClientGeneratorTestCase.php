<?php
/**
 * Test for net::stubbles::service::soap::stubSoapClientGenerator.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @version     $Id: stubSoapClientGeneratorTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapClientGenerator',
                      'net::stubbles::service::soap::stubAbstractSoapClient'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 */
class WsdlSoapClient extends stubAbstractSoapClient
{
    /**
     * switch whether wsdl is supported or not
     *
     * @var  bool
     */
    protected $supportsWsdl;

    /**
     * checks whether the client supports WSDL or not
     *
     * @return  bool
     */
    public function supportsWsdl()
    {
        return $this->supportsWsdl;
    }

    /**
     * returns a list of functions provided by the soap service
     *
     * @return  array<string>
     */
    public function getFunctions() { }

    /**
     * returns a list of types the soap service uses for interaction
     *
     * @return  array<string>
     */
    public function getTypes() { }

    /**
     * invoke method call
     *
     * @param   string  $method  name of method to invoke
     * @param   array   $args    list of arguments for method
     * @return  mixed
     * @throws  stubSoapException
     */
    public function invoke($method, array $args = array()) { }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 */
class TrueWsdlSoapClient extends WsdlSoapClient
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->supportsWsdl = true;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 */
class FalseWsdlSoapClient extends WsdlSoapClient
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->supportsWsdl = false;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 */
class FailsOnConstruction extends WsdlSoapClient
{
    /**
     * constructor
     *
     * @throws  Exception
     */
    public function __construct()
    {
        throw new Exception();
    }
}
/**
 * Tests for net::stubbles::service::soap::stubSoapClientGenerator.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @group       service_soap
 */
class stubSoapClientGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * temporary client store
     *
     * @var  stubSoapClientGenerator
     */
    protected $soapClientGenerator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->soapClientGenerator = new stubSoapClientGenerator();
    }

    /**
     * exception should handle a fault correctly
     *
     * @test
     */
    public function nativeSoapClientAvailableByDefault()
    {
        if (extension_loaded('soap') === false) {
            $this->markTestSkipped('Native soap client not enabled as soap extension is not available.');
        }
        
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertEquals(array('net::stubbles::service::soap::native::stubNativeSoapClient' => 'net::stubbles::service::soap::native::stubNativeSoapClient'),
                            $this->soapClientGenerator->getAvailableClients()
        );
        $client = $this->soapClientGenerator->forConfig($config);
        $this->assertInstanceOf('stubNativeSoapClient', $client);
        $this->assertSame($config, $client->getConfig());
    }

    /**
     * in case no client is available a runtime exception is thrown
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function noClientAvailableThrowsRuntimeException()
    {
        $this->soapClientGenerator->removeClient('net::stubbles::service::soap::native::stubNativeSoapClient');
        $this->assertEquals(array(), $this->soapClientGenerator->getAvailableClients());
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $client = $this->soapClientGenerator->forConfig($config);
    }

    /**
     * adding an invalid client class throws an illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function addInvalidClientThrowsIllegalArgumentException()
    {
        $this->soapClientGenerator->addClient(new ReflectionClass('stdClass'));
    }

    /**
     * make sure the correct client will be used
     *
     * @test
     */
    public function wsdlBehaviour()
    {
        $this->soapClientGenerator->removeClient('net::stubbles::service::soap::native::stubNativeSoapClient');
        $this->soapClientGenerator->addClient(new ReflectionClass('FailsOnConstruction'));
        $this->soapClientGenerator->addClient(new ReflectionClass('FalseWsdlSoapClient'));
        $this->soapClientGenerator->addClient(new ReflectionClass('TrueWsdlSoapClient'));
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $config->useWsdl(true);
        $client = $this->soapClientGenerator->forConfig($config, true);
        $this->assertInstanceOf('TrueWsdlSoapClient', $client);
    }

    /**
     * if no suited client is available a runtime exception is thrown
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function wsdlBehaviourNoWsdlSupportAvailable()
    {
        $this->soapClientGenerator->removeClient('net::stubbles::service::soap::native::stubNativeSoapClient');
        $this->soapClientGenerator->addClient(new ReflectionClass('FailsOnConstruction'));
        $this->soapClientGenerator->addClient(new ReflectionClass('FalseWsdlSoapClient'));
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $config->useWsdl(true);
        $client = $this->soapClientGenerator->forConfig($config, true);
    }
}
?>