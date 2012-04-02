<?php
/**
 * Test for net::stubbles::service::soap::stubSoapClientConfiguration.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @version     $Id: stubSoapClientConfigurationTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapClientConfiguration');
if (extension_loaded('soap') === false) {
    define('SOAP_1_2', 1);
    define('SOAP_LITERAL', 2);
    define('SOAP_DOCUMENT', 3);
}
/**
 * Tests for net::stubbles::service::soap::stubSoapClientConfiguration.
 *
 * @package     stubbles
 * @subpackage  service_soap_test
 * @group       service_soap
 */
class stubSoapClientConfigurationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for $endPoint
     *
     * @test
     */
    public function endPointAsString()
    {
        $config   = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $endPoint = $config->getEndPoint();
        $this->assertInstanceOf('stubHTTPURL', $endPoint);
        $this->assertEquals('http://example.net/', $endPoint->get());
        $this->assertEquals('urn:foo', $config->getUri());
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for $endPoint
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalEndPointAsString()
    {
        $config = new stubSoapClientConfiguration('', 'urn:foo');
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for $endPoint
     *
     * @test
     */
    public function endPointAsHTTPURLInstance()
    {
        $endPoint = stubHTTPURL::fromString('http://example.net/');
        $config   = new stubSoapClientConfiguration($endPoint, 'urn:foo');
        $test     = $config->getEndPoint();
        $this->assertSame($endPoint, $test);
        $this->assertEquals('urn:foo', $config->getUri());
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http.stubHTTPURL
     * are accepted as value for $endPoint
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function endPointInvalidInstance()
    {
        $config = new stubSoapClientConfiguration(new stdClass(), 'urn:foo');
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for location
     *
     * @test
     */
    public function locationAsString()
    {
        $config   = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertFalse($config->hasLocation());
        $this->assertNull($config->getLocation());
        $this->assertSame($config, $config->setLocation('http://example.org/'));
        $this->assertTrue($config->hasLocation());
        $location = $config->getLocation();
        $this->assertInstanceOf('stubHTTPURL', $location);
        $this->assertEquals('http://example.org/', $location->get());
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for location
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalLocationAsString()
    {
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $config->setLocation('');
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http::stubHTTPURL
     * are accepted as value for location
     *
     * @test
     */
    public function locationAsHTTPURLInstance()
    {
        $location = stubHTTPURL::fromString('http://example.org/');
        $config   = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertSame($config, $config->setLocation($location));
        $this->assertTrue($config->hasLocation());
        $this->assertSame($location, $config->getLocation());
    }

    /**
     * test that only strings and instances of net::stubbles::peer::http.stubHTTPURL
     * are accepted as value for location
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function locationInvalidInstance()
    {
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $config->setLocation(new stdClass());
    }

    /**
     * wsdl defaults to false, but may be reconfigured
     *
     * @test
     */
    public function wsdlProperty()
    {
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertTrue($config->usesWsdl());
        $this->assertSame($config, $config->useWSDL(false));
        $this->assertFalse($config->usesWsdl());
    }

    /**
     * version defaults to null, but may be reconfigured
     *
     * @test
     */
    public function versionProperty()
    {
        $config =stubSoapClientConfiguration::create('http://example.net/', 'urn:foo');
        $this->assertNull($config->getVersion());
        $this->assertSame($config, $config->setVersion(SOAP_1_2));
        $this->assertEquals(SOAP_1_2, $config->getVersion());
    }

    /**
     * data encoding defaults to iso-8859-1, but may be reconfigured
     *
     * @test
     */
    public function dataEncodingProperty()
    {
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertEquals('iso-8859-1', $config->getDataEncoding());
        $this->assertSame($config, $config->setDataEncoding('utf-8'));
        $this->assertEquals('utf-8', $config->getDataEncoding());
    }

    /**
     * requestStyle defaults to null, but may be reconfigured
     *
     * @test
     */
    public function requestStyleProperty()
    {
        $config =stubSoapClientConfiguration::create('http://example.net/', 'urn:foo');
        $this->assertNull($config->getRequestStyle());
        $this->assertSame($config, $config->setRequestStyle(SOAP_DOCUMENT));
        $this->assertEquals(SOAP_DOCUMENT, $config->getRequestStyle());
    }

    /**
     * usage defaults to null, but may be reconfigured
     *
     * @test
     */
    public function usageProperty()
    {
        $config =stubSoapClientConfiguration::create('http://example.net/', 'urn:foo');
        $this->assertNull($config->getUsage());
        $this->assertSame($config, $config->setUsage(SOAP_LITERAL));
        $this->assertEquals(SOAP_LITERAL, $config->getUsage());
    }

    /**
     * assert that class mapping methods work as expected
     *
     * @test
     */
    public function classMappingProperty()
    {
        $config = new stubSoapClientConfiguration('http://example.net/', 'urn:foo');
        $this->assertFalse($config->hasClassMapping());
        $this->assertEquals(array(), $config->getClassMapping());
        $this->assertSame($config, $config->registerClassMapping('foo', new ReflectionClass('stdClass')));
        $this->assertTrue($config->hasClassMapping());
        $this->assertEquals(array('foo' => 'stdClass'), $config->getClassMapping());
    }
}
?>