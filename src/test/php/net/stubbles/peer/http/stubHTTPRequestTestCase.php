<?php
/**
 * Test for net::stubbles::peer::http::stubHTTPRequest.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @version     $Id: stubHTTPRequestTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::http::stubHTTPRequest');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 */
class TeststubHTTPRequest extends stubHTTPRequest
{
    /**
     * access to protected method
     *
     * @return  stubSocket
     */
    public function callCreateSocket()
    {
        return $this->createSocket();
    }

    /**
     * access to protected method
     *
     * @param  stubSocket  $socket   the socket to write headers to
     * @param  string      $method   HTTP method
     * @param  string      $version  HTTP version
     */
    public function callProcessHeader(stubSocket $socket, $method, $version)
    {
        $this->processHeader($socket, $method, $version);
    }
}
/**
 * Test for net::stubbles::peer::http::stubHTTPRequest.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @group       peer
 * @group       peer_http
 */
class stubHTTPRequestTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be tested
     *
     * @var  stubHTTPRequest
     */
    protected $httpRequest;
    /**
     * URL instance to be used
     *
     * @var  stubHTTPURL
     */
    protected $httpURL;
    /**
     * header list instance
     *
     * @var  stubHeaderList
     */
    protected $headerList;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->httpURL     = stubHTTPURL::fromString('http://example.com/');
        $this->headerList  = new stubHeaderList();
        $this->httpRequest = $this->getMock('stubHTTPRequest',
                                            array('createSocket', 'processHeader'),
                                            array($this->httpURL, $this->headerList, 2)
                             );
    }

    /**
     * initializing a get request should return a response object
     *
     * @test
     */
    public function initializeGetRequest()
    {
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())
                   ->method('write')
                   ->with($this->equalTo(stubHTTPConnection::END_OF_LINE));
        $this->httpRequest->expects($this->once())
                             ->method('createSocket')
                             ->will($this->returnValue($mockSocket));
        $this->httpRequest->expects($this->once())
                             ->method('processHeader')
                             ->with($this->equalTo($mockSocket),
                                    $this->equalTo(stubHTTPRequest::METHOD_GET),
                                    $this->equalTo(stubHTTPRequest::VERSION_1_1)
                               )
                             ->will($this->returnValue($mockSocket));
        $response = $this->httpRequest->get(stubHTTPRequest::VERSION_1_1);
        $this->assertInstanceOf('stubHTTPResponse', $response);
        $this->assertSame($mockSocket, $response->getSocket());
    }

    /**
     * initializing a head request should return a response object
     *
     * @test
     */
    public function initializeHeadRequest()
    {
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())
                   ->method('write')
                   ->with($this->equalTo('Connection: close' . stubHTTPConnection::END_OF_LINE . stubHTTPConnection::END_OF_LINE));
        $this->httpRequest->expects($this->once())
                             ->method('createSocket')
                             ->will($this->returnValue($mockSocket));
        $this->httpRequest->expects($this->once())
                             ->method('processHeader')
                             ->with($this->equalTo($mockSocket),
                                    $this->equalTo(stubHTTPRequest::METHOD_HEAD),
                                    $this->equalTo(stubHTTPRequest::VERSION_1_1)
                               )
                             ->will($this->returnValue($mockSocket));
        $response = $this->httpRequest->head(stubHTTPRequest::VERSION_1_1);
        $this->assertInstanceOf('stubHTTPResponse', $response);
        $this->assertSame($mockSocket, $response->getSocket());
    }

    /**
     * initializing a post request should return a response object
     *
     * @test
     */
    public function initializePostRequestWithoutBody()
    {
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->at(0))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(1))
                   ->method('write')
                   ->with($this->equalTo(''));
        $this->httpRequest->expects($this->once())
                             ->method('createSocket')
                             ->will($this->returnValue($mockSocket));
        $this->httpRequest->expects($this->once())
                             ->method('processHeader')
                             ->with($this->equalTo($mockSocket),
                                    $this->equalTo(stubHTTPRequest::METHOD_POST),
                                    $this->equalTo(stubHTTPRequest::VERSION_1_1)
                               )
                             ->will($this->returnValue($mockSocket));
        $response = $this->httpRequest->post(stubHTTPRequest::VERSION_1_1);
        $this->assertInstanceOf('stubHTTPResponse', $response);
        $this->assertSame($mockSocket, $response->getSocket());
    }

    /**
     * initializing a post request should return a response object
     *
     * @test
     */
    public function initializePostRequestWithBody()
    {
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->at(0))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(1))
                   ->method('write')
                   ->with($this->equalTo('foo=bar+baz&'));
        $this->httpRequest->expects($this->once())
                             ->method('createSocket')
                             ->will($this->returnValue($mockSocket));
        $this->httpRequest->expects($this->once())
                             ->method('processHeader')
                             ->with($this->equalTo($mockSocket),
                                    $this->equalTo(stubHTTPRequest::METHOD_POST),
                                    $this->equalTo(stubHTTPRequest::VERSION_1_1)
                               )
                             ->will($this->returnValue($mockSocket));
        $this->httpRequest->preparePost(array('foo' => 'bar baz'));
        $response = $this->httpRequest->post(stubHTTPRequest::VERSION_1_1);
        $this->assertInstanceOf('stubHTTPResponse', $response);
        $this->assertSame($mockSocket, $response->getSocket());
        $this->assertEquals('application/x-www-form-urlencoded', $this->headerList->get('Content-Type'));
        $this->assertEquals(12, $this->headerList->get('Content-Length'));
    }

    /**
     * socket should be created with correct host and port and prefix
     *
     * @test
     */
    public function socketCreation()
    {
        $httpRequest = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $socket      = $httpRequest->callCreateSocket();
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(80, $socket->getPort());
        $this->assertNull($socket->getPrefix());
    }

    /**
     * socket should be created with correct host and port and prefix
     *
     * @test
     */
    public function socketCreationWithSsl()
    {
        $this->httpURL = stubHTTPURL::fromString('https://example.com/');
        $httpRequest   = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $socket        = $httpRequest->callCreateSocket();
        $this->assertEquals('example.com', $socket->getHost());
        $this->assertEquals(443, $socket->getPort());
        $this->assertEquals('ssl://', $socket->getPrefix());
    }

    /**
     * process headers for get request
     *
     * @test
     */
    public function processHeaderGet()
    {
        $httpRequest = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())->method('setTimeout')->with($this->equalTo(2));
        $mockSocket->expects($this->at(2))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPRequest::METHOD_GET . ' / ' . stubHTTPRequest::VERSION_1_0 . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(3))
                   ->method('write')
                   ->with($this->equalTo('Host: example.com' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(4))
                   ->method('write')
                   ->with($this->equalTo('User-Agent: stubbles HTTP Client' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(5))
                   ->method('write')
                   ->with($this->equalTo('X-Binford: More power!' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(6))
                   ->method('write')
                   ->with($this->stringStartsWith('Date: '));
        $httpRequest->callProcessHeader($mockSocket, stubHTTPRequest::METHOD_GET, stubHTTPRequest::VERSION_1_0);
    }

    /**
     * process headers for head request
     *
     * @test
     */
    public function processHeaderHead()
    {
        $this->headerList->putUserAgent('TestSuite');
        $httpRequest = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())->method('setTimeout')->with($this->equalTo(2));
        $mockSocket->expects($this->at(2))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPRequest::METHOD_HEAD . ' / ' . stubHTTPRequest::VERSION_1_1 . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(3))
                   ->method('write')
                   ->with($this->equalTo('Host: example.com' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(4))
                   ->method('write')
                   ->with($this->equalTo('User-Agent: TestSuite' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(5))
                   ->method('write')
                   ->with($this->equalTo('X-Binford: More power!' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(6))
                   ->method('write')
                   ->with($this->stringStartsWith('Date: '));
        $httpRequest->callProcessHeader($mockSocket, stubHTTPRequest::METHOD_HEAD, stubHTTPRequest::VERSION_1_1);
    }

    /**
     * process headers for post request
     *
     * @test
     */
    public function processHeaderPost()
    {
        $this->headerList->putUserAgent('TestSuite');
        $httpRequest = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())->method('setTimeout')->with($this->equalTo(2));
        $mockSocket->expects($this->at(2))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPRequest::METHOD_POST . ' / ' . stubHTTPRequest::VERSION_1_1 . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(3))
                   ->method('write')
                   ->with($this->equalTo('Host: example.com' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(4))
                   ->method('write')
                   ->with($this->equalTo('User-Agent: TestSuite' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(5))
                   ->method('write')
                   ->with($this->equalTo('X-Binford: More power!' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(6))
                   ->method('write')
                   ->with($this->stringStartsWith('Date: '));
        $httpRequest->callProcessHeader($mockSocket, stubHTTPRequest::METHOD_POST, stubHTTPRequest::VERSION_1_1);
    }

    /**
     * process headers for request with illegal method and version
     *
     * @test
     */
    public function processHeaderIllegalMethodAndVersion()
    {
        $httpRequest = new TeststubHTTPRequest($this->httpURL, $this->headerList, 2);
        $mockSocket = $this->getMock('stubSocket', array(), array('example.com'));
        $mockSocket->expects($this->once())->method('setTimeout')->with($this->equalTo(2));
        $mockSocket->expects($this->at(2))
                   ->method('write')
                   ->with($this->equalTo(stubHTTPRequest::METHOD_GET . ' / ' . stubHTTPRequest::VERSION_1_1 . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(3))
                   ->method('write')
                   ->with($this->equalTo('Host: example.com' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(4))
                   ->method('write')
                   ->with($this->equalTo('User-Agent: stubbles HTTP Client' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(5))
                   ->method('write')
                   ->with($this->equalTo('X-Binford: More power!' . stubHTTPConnection::END_OF_LINE));
        $mockSocket->expects($this->at(6))
                   ->method('write')
                   ->with($this->stringStartsWith('Date: '));
        $httpRequest->callProcessHeader($mockSocket, 'illegal', 'illegal');
    }
}
?>