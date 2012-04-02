<?php
/**
 * Test for net::stubbles::service::soap::native::stubNativeSoapClient.
 *
 * @package     stubbles
 * @subpackage  service_soap_native_test
 * @version     $Id: stubNativeSoapClientTestCase.php 2460 2010-01-18 15:20:23Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::native::stubNativeSoapClient');
/**
 * Tests for net::stubbles::service::soap::native::stubNativeSoapClient.
 *
 * @package     stubbles
 * @subpackage  service_soap_native_test
 * @group       service_soap
 */
class stubNativeSoapClientTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubSoapClientConfiguration
     */
    protected $soapClientConfig;
    /**
     * mocked soap client
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSoapClient;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (extension_loaded('soap') === false) {
            $this->markTestSkipped('net::stubbles::service::soap::native::stubNativeSoapClient requires PHP-extension "soap".');
        }
        
        $this->soapClientConfig = new stubSoapClientConfiguration('http://user:password@example.net/soap.wsdl', 'http://example.org/');
        $this->mockSoapClient   = $this->getMock('SoapClient',
                                                 array('__getFunctions',
                                                       '__getTypes',
                                                       '__soapCall',
                                                       '__getLastRequestHeaders',
                                                       '__getLastRequest',
                                                       '__getLastResponseHeaders',
                                                       '__getLastResponse'
                                                 ),
                                                 array(null, array('uri'      => 'http://example.net',
                                                                   'location' => 'http://example.net'
                                                                   )
                                                 )
                                  );
    }

    /**
     * test that only valid versions are accepted
     *
     * @test
     */
    public function version()
    {
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertNull($client->getConfig()->getVersion());
        
        $this->soapClientConfig->setVersion(SOAP_1_1);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_1_1, $client->getConfig()->getVersion());
        
        $this->soapClientConfig->setVersion(SOAP_1_2);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_1_2, $client->getConfig()->getVersion());
    }

    /**
     * test that only valid versions are accepted
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalVersion()
    {
        $this->soapClientConfig->setVersion('illegal');
        $client = new stubNativeSoapClient($this->soapClientConfig);
    }

    /**
     * test that only valid request styles are accepted
     *
     * @test
     */
    public function requestStyle()
    {
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_RPC, $client->getConfig()->getRequestStyle());
        
        $this->soapClientConfig->setRequestStyle(SOAP_RPC);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_RPC, $client->getConfig()->getRequestStyle());
        
        $this->soapClientConfig->setRequestStyle(SOAP_DOCUMENT);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_DOCUMENT, $client->getConfig()->getRequestStyle());
    }

    /**
     * test that only valid request styles are accepted
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalRequestStyle()
    {
        $this->soapClientConfig->setRequestStyle('illegal');
        $client = new stubNativeSoapClient($this->soapClientConfig);
    }

    /**
     * test that only valid usage values are accepted
     *
     * @test
     */
    public function usage()
    {
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_ENCODED, $client->getConfig()->getUsage());
        
        $this->soapClientConfig->setUsage(SOAP_ENCODED);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_ENCODED, $client->getConfig()->getUsage());
        
        $this->soapClientConfig->setUsage(SOAP_LITERAL);
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertSame($this->soapClientConfig, $client->getConfig());
        $this->assertEquals(SOAP_LITERAL, $client->getConfig()->getUsage());
    }

    /**
     * test that only valid usage values are accepted
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illagelUsage()
    {
        $this->soapClientConfig->setUsage('illegal');
        $client = new stubNativeSoapClient($this->soapClientConfig);
    }

    /**
     * native client always supports WSDL
     *
     * @test
     */
    public function alwaysSupportsWSDL()
    {
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertTrue($client->supportsWSDL());
    }

    /**
     * before invocation of the soap method there should be no debug data
     *
     * @test
     */
    public function debugDataIsEmptyBeforeInvocation()
    {
        $client = new stubNativeSoapClient($this->soapClientConfig);
        $this->assertEquals(array(), $client->getDebugData());
    }

    /**
     * getFunctions() calls data from native client
     *
     * @test
     */
    public function getFunctionsCallsNativeClient()
    {
        $client = $this->getMock('stubNativeSoapClient', array('createClientInstance'), array($this->soapClientConfig));
        $client->expects($this->once())
               ->method('createClientInstance')
               ->with($this->equalTo('http://user:password@example.net:80/soap.wsdl'),
                      $this->equalTo(array('exceptions'         => false,
                                           'trace'              => true,
                                           'encoding'           => $this->soapClientConfig->getDataEncoding(),
                                           'connection_timeout' => 2,
                                           'login'              => 'user',
                                           'password'           => 'password'
                                     )
                      )
                 )
               ->will($this->returnValue($this->mockSoapClient));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getFunctions')
                             ->will($this->returnValue(array('DslStatus check(string $rufnummer, int $mandantId, string $caller)')));
        $this->assertEquals(array('DslStatus check(string $rufnummer, int $mandantId, string $caller)'),
                            $client->getFunctions()
        );
    }

    /**
     * getTypes() calls data from native client
     *
     * @test
     */
    public function getTypesCallsNativeClient()
    {
        $this->soapClientConfig = new stubSoapClientConfiguration('http://example.net/soap.wsdl', 'http://example.org/');
        $this->soapClientConfig->setLocation('http://example.com/')
                               ->setVersion(SOAP_1_2)
                               ->registerClassMapping('stdClass', new ReflectionClass('stdClass'));
        $client = $this->getMock('stubNativeSoapClient', array('createClientInstance'), array($this->soapClientConfig));
        $this->assertSame($client, $client->timeout(5));
        $client->expects($this->once())
               ->method('createClientInstance')
               ->with($this->equalTo('http://example.net:80/soap.wsdl'),
                      $this->equalTo(array('exceptions'         => false,
                                           'trace'              => true,
                                           'encoding'           => $this->soapClientConfig->getDataEncoding(),
                                           'connection_timeout' => 5,
                                           'version'            => SOAP_1_2,
                                           'classmap'           => array('stdClass' => 'stdClass'),
                                           'location'           => 'http://example.com:80/'
                                     )
                      )
                 )
               ->will($this->returnValue($this->mockSoapClient));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getTypes')
                             ->will($this->returnValue(array("struct DslStatusCheckException {\n}")));
        $this->assertEquals(array("struct DslStatusCheckException {\n}"),
                            $client->getTypes()
        );
    }

    /**
     * succesful invocation returns result
     *
     * @test
     */
    public function invokeSuccessful()
    {
        $this->soapClientConfig = new stubSoapClientConfiguration('http://example.net/', 'http://example.org/');
        $this->soapClientConfig->useWsdl(false);
        $client = $this->getMock('stubNativeSoapClient', array('createClientInstance'), array($this->soapClientConfig));
        $client->expects($this->once())
               ->method('createClientInstance')
               ->with($this->equalTo(null),
                      $this->equalTo(array('exceptions'         => false,
                                           'trace'              => true,
                                           'encoding'           => $this->soapClientConfig->getDataEncoding(),
                                           'connection_timeout' => 2,
                                           'location'           => 'http://example.net:80/',
                                           'uri'                => 'http://example.org/',
                                           'style'              => $this->soapClientConfig->getRequestStyle(),
                                           'use'                => $this->soapClientConfig->getUsage()
                                     )
                      )
                 )
               ->will($this->returnValue($this->mockSoapClient));
        $this->mockSoapClient->expects($this->once())
                             ->method('__soapCall')
                             ->with($this->equalTo('soapMethod'),
                                    $this->equalTo(array('foo'))
                               )
                             ->will($this->returnValue('baz'));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequestHeaders')
                             ->will($this->returnValue(array('requestHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequest')
                             ->will($this->returnValue('requestXml'));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponseHeaders')
                             ->will($this->returnValue(array('responseHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponse')
                             ->will($this->returnValue('responseXml'));
        $this->assertEquals('baz',
                            $client->invoke('soapMethod', array('foo'))
        );
        $this->assertEquals(array('endPoint'           => 'http://example.net:80/',
                                  'usedWsdl'           => false,
                                  'lastMethod'         => 'soapMethod',
                                  'lastArgs'           => array('foo'),
                                  'lastRequestHeader'  => array('requestHeader'),
                                  'lastRequest'        => 'requestXml',
                                  'lastResponseHeader' => array('responseHeader'),
                                  'lastResponse'       => 'responseXml'
                            ),
                            $client->getDebugData()
        );
    }

    /**
     * succesful invocation returns result
     *
     * @test
     */
    public function invokeSuccessfulWithParsing()
    {
        $this->soapClientConfig = new stubSoapClientConfiguration('http://example.net/', 'http://example.org/');
        $client = $this->getMock('stubNativeSoapClient', array('createClientInstance'), array($this->soapClientConfig));
        $this->assertSame($client, $client->timeout(5));
        $client->expects($this->once())
               ->method('createClientInstance')
               ->with($this->equalTo('http://example.net:80/'),
                      $this->equalTo(array('exceptions'         => false,
                                           'trace'              => true,
                                           'encoding'           => $this->soapClientConfig->getDataEncoding(),
                                           'connection_timeout' => 5,
                                     )
                      )
                 )
               ->will($this->returnValue($this->mockSoapClient));
        $result = new stdClass;
        $result->out = 'baz';
        $this->mockSoapClient->expects($this->once())
                             ->method('__soapCall')
                             ->with($this->equalTo('soapMethod'),
                                    $this->equalTo(array('parameters' => array('in0' => 'bar')))
                               )
                             ->will($this->returnValue($result));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequestHeaders')
                             ->will($this->returnValue(array('requestHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequest')
                             ->will($this->returnValue('requestXml'));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponseHeaders')
                             ->will($this->returnValue(array('responseHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponse')
                             ->will($this->returnValue('responseXml'));
        $this->assertEquals('baz',
                            $client->invoke('soapMethod',
                                            array('in0' => 'bar'),
                                            array('asParameters'      => 'parameters',
                                                  'parseFromStdClass' => 'out'
                                            )
                            )
        );
        $this->assertEquals(array('endPoint'           => 'http://example.net:80/',
                                  'usedWsdl'           => true,
                                  'lastMethod'         => 'soapMethod',
                                  'lastArgs'           => array('parameters' => array('in0' => 'bar')),
                                  'lastRequestHeader'  => array('requestHeader'),
                                  'lastRequest'        => 'requestXml',
                                  'lastResponseHeader' => array('responseHeader'),
                                  'lastResponse'       => 'responseXml'
                            ),
                            $client->getDebugData()
        );
    }

    /**
     * failing invocation throws exception
     *
     * @test
     */
    public function invokeFails()
    {
        $this->soapClientConfig = new stubSoapClientConfiguration('http://example.net/', 'http://example.org/');
        $this->soapClientConfig->useWsdl(false);
        $client = $this->getMock('stubNativeSoapClient', array('createClientInstance'), array($this->soapClientConfig));
        $client->expects($this->once())
               ->method('createClientInstance')
               ->with($this->equalTo(null),
                      $this->equalTo(array('exceptions'         => false,
                                           'trace'              => true,
                                           'encoding'           => $this->soapClientConfig->getDataEncoding(),
                                           'connection_timeout' => 2,
                                           'location'           => 'http://example.net:80/',
                                           'uri'                => 'http://example.org/',
                                           'style'              => $this->soapClientConfig->getRequestStyle(),
                                           'use'                => $this->soapClientConfig->getUsage()
                                     )
                      )
                 )
               ->will($this->returnValue($this->mockSoapClient));
        $this->mockSoapClient->expects($this->once())
                             ->method('__soapCall')
                             ->with($this->equalTo('soapMethod'),
                                    $this->equalTo(array('parameters' => array('in0' => 'bar')))
                               )
                             ->will($this->returnValue(new SoapFault('code', 'string', 'actor', 'detail')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequestHeaders')
                             ->will($this->returnValue(array('requestHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastRequest')
                             ->will($this->returnValue('requestXml'));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponseHeaders')
                             ->will($this->returnValue(array('responseHeader')));
        $this->mockSoapClient->expects($this->once())
                             ->method('__getLastResponse')
                             ->will($this->returnValue('responseXml'));
        try {
            $client->invoke('soapMethod', array('in0' => 'bar'), array('asParameters' => 'parameters'));
            $this->fail('Expected stubSoapException, got none.');
        } catch (stubSoapException $se) {
            $soapFault = $se->getSoapFault();
            $this->assertEquals('code', $soapFault->getFaultCode());
            $this->assertEquals('string', $soapFault->getFaultString());
            $this->assertEquals('actor', $soapFault->getFaultActor());
            $this->assertEquals('detail', $soapFault->getDetail());
            $this->assertEquals(array('endPoint'           => 'http://example.net:80/',
                                      'usedWsdl'           => false,
                                      'lastMethod'         => 'soapMethod',
                                      'lastArgs'           => array('parameters' => array('in0' => 'bar')),
                                      'lastRequestHeader'  => array('requestHeader'),
                                      'lastRequest'        => 'requestXml',
                                      'lastResponseHeader' => array('responseHeader'),
                                      'lastResponse'       => 'responseXml'
                                ),
                                $client->getDebugData()
            );
        }
    }
}
?>