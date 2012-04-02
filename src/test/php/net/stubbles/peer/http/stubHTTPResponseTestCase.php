<?php
/**
 * Test for net::stubbles::peer::http::stubHTTPResponse.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @version     $Id: stubHTTPResponseTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::http::stubHTTPResponse');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @group       peer
 * @group       peer_http
 */
class TeststubHTTPResponse extends stubHTTPResponse
{
    /**
     * preset a header list instance
     *
     * @param  stubHeaderList  $headers
     */
    public function setHeaderList(stubHeaderList $headers)
    {
        $this->headers = $headers;
    }

    /**
     * access to protected method
     *
     * @return  string
     */
    public function callReadChunked()
    {
        return $this->readChunked();
    }

    /**
     * access to protected method
     *
     * @param   int     $readLength  expected length of response body
     * @return  string
     */
    public function callReadDefault($readLength)
    {
        return $this->readDefault($readLength);
    }

    /**
     * access to protected method
     *
     * @param  string  $head  first line of response
     */
    public function callParseHead($head)
    {
        $this->parseHead($head);
    }
}
/**
 * Test for net::stubbles::peer::http::stubHTTPResponse.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 */
class stubHTTPResponseTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be tested
     *
     * @var  stubHTTPResponse
     */
    protected $httpResponse;
    /**
     * mocked socket instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSocket;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockSocket   = $this->getMock('stubSocket', array(), array('example.com'));
        $this->httpResponse = new TeststubHTTPResponse($this->mockSocket);
    }

    /**
     * read a chunked response
     *
     * @test
     */
    public function readChunkedResponse()
    {
        $httpResponse = $this->getMock('stubHTTPResponse',
                                       array('parseHead',
                                             'readChunked',
                                             'readDefault'
                                       ),
                                       array($this->mockSocket)
                        );
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->will($this->returnValue('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $this->mockSocket->expects($this->exactly(4))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->mockSocket->expects($this->exactly(3))
                         ->method('read')
                         ->will($this->onConsecutiveCalls('Host: example.com' . stubHTTPConnection::END_OF_LINE,
                                                          'Transfer-Encoding: chunked' . stubHTTPConnection::END_OF_LINE,
                                                          stubHTTPConnection::END_OF_LINE
                                       )
                           );
        $httpResponse->expects($this->once())
                     ->method('parseHead')
                     ->with($this->equalTo('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $httpResponse->expects($this->once())
                     ->method('readChunked')
                     ->will($this->returnValue('body'));
        $httpResponse->expects($this->never())
                     ->method('readDefault');
        $this->assertNull($httpResponse->getHeader());
        $this->assertEquals('', $httpResponse->getBody());
        $this->assertSame($httpResponse, $httpResponse->read());
        $this->assertInstanceOf('stubHeaderList', $httpResponse->getHeader());
        $this->assertEquals('example.com', $httpResponse->getHeader()->get('Host'));
        $this->assertEquals('body', $httpResponse->getBody());
    }

    /**
     * read a default response without a content-length header
     *
     * @test
     */
    public function readDefaultResponseWithoutContentLengthHeader()
    {
        $httpResponse = $this->getMock('stubHTTPResponse',
                                       array('parseHead',
                                             'readChunked',
                                             'readDefault'
                                       ),
                                       array($this->mockSocket)
                        );
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->will($this->returnValue('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $this->mockSocket->expects($this->exactly(3))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->mockSocket->expects($this->exactly(2))
                         ->method('read')
                         ->will($this->onConsecutiveCalls('Host: example.com' . stubHTTPConnection::END_OF_LINE,
                                                          stubHTTPConnection::END_OF_LINE
                                       )
                           );
        $httpResponse->expects($this->once())
                     ->method('parseHead')
                     ->with($this->equalTo('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $httpResponse->expects($this->never())
                     ->method('readChunked');
        $httpResponse->expects($this->once())
                     ->method('readDefault')
                     ->with($this->equalTo(4096))
                     ->will($this->returnValue('body'));
        $this->assertNull($httpResponse->getHeader());
        $this->assertEquals('', $httpResponse->getBody());
        $this->assertSame($httpResponse, $httpResponse->read());
        $this->assertInstanceOf('stubHeaderList', $httpResponse->getHeader());
        $this->assertEquals('example.com', $httpResponse->getHeader()->get('Host'));
        $this->assertEquals('body', $httpResponse->getBody());
    }

    /**
     * read a default response with a content-length header
     *
     * @test
     */
    public function readDefaultResponseWithContentLengthHeader()
    {
        $httpResponse = $this->getMock('stubHTTPResponse',
                                       array('parseHead',
                                             'readChunked',
                                             'readDefault'
                                       ),
                                       array($this->mockSocket)
                        );
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->will($this->returnValue('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $this->mockSocket->expects($this->exactly(4))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->mockSocket->expects($this->exactly(3))
                         ->method('read')
                         ->will($this->onConsecutiveCalls('Host: example.com' . stubHTTPConnection::END_OF_LINE,
                                                          'Content-Length: 2048' . stubHTTPConnection::END_OF_LINE,
                                                          stubHTTPConnection::END_OF_LINE
                                       )
                           );
        $httpResponse->expects($this->once())
                     ->method('parseHead')
                     ->with($this->equalTo('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $httpResponse->expects($this->never())
                     ->method('readChunked');
        $httpResponse->expects($this->once())
                     ->method('readDefault')
                     ->with($this->equalTo(2048))
                     ->will($this->returnValue('body'));
        $this->assertNull($httpResponse->getHeader());
        $this->assertEquals('', $httpResponse->getBody());
        $this->assertSame($httpResponse, $httpResponse->read());
        $this->assertInstanceOf('stubHeaderList', $httpResponse->getHeader());
        $this->assertEquals('example.com', $httpResponse->getHeader()->get('Host'));
        $this->assertEquals('body', $httpResponse->getBody());
    }

    /**
     * reading the header only
     *
     * @test
     */
    public function readHead()
    {
        $httpResponse = $this->getMock('stubHTTPResponse',
                                       array('parseHead',
                                             'readChunked',
                                             'readDefault'
                                       ),
                                       array($this->mockSocket)
                        );
        $this->mockSocket->expects($this->once())
                         ->method('readLine')
                         ->will($this->returnValue('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $this->mockSocket->expects($this->exactly(4))
                         ->method('eof')
                         ->will($this->returnValue(false));
        $this->mockSocket->expects($this->exactly(3))
                         ->method('read')
                         ->will($this->onConsecutiveCalls('Host: example.com' . stubHTTPConnection::END_OF_LINE,
                                                          'Content-Length: 2048' . stubHTTPConnection::END_OF_LINE,
                                                          stubHTTPConnection::END_OF_LINE
                                       )
                           );
        $httpResponse->expects($this->once())
                     ->method('parseHead')
                     ->with($this->equalTo('HTTP/1.1 200 OK' . stubHTTPConnection::END_OF_LINE));
        $this->assertNull($httpResponse->getHeader());
        $this->assertEquals('', $httpResponse->getBody());
        $this->assertSame($httpResponse, $httpResponse->readHeader());
        $this->assertInstanceOf('stubHeaderList', $httpResponse->getHeader());
        $this->assertEquals('example.com', $httpResponse->getHeader()->get('Host'));
    }

    /**
     * reading body without previous reading of headers throws exception
     *
     * @test
     * @expectedException  stubIllegalAccessException
     */
    public function readBodyWithoutReadingHeadersThrowsException()
    {
        $this->assertSame($this->httpResponse, $this->httpResponse->readBody());
    }

    /**
     * all types should be returned
     *
     * @test
     */
    public function allTypesAreReturned()
    {
        $this->assertEquals(5, count($this->httpResponse->getTypes()));
    }

    /**
     * chunked response reading algorithm
     *
     * @test
     */
    public function chunkedReading()
    {
        $headerList = new stubHeaderList();
        $headerList->put('Transfer-Encoding', 'chunked');
        $this->httpResponse->setHeaderList($headerList);
        $this->mockSocket->expects($this->exactly(3))
                         ->method('read')
                         ->with($this->equalTo(1024))
                         ->will($this->onConsecutiveCalls(dechex(3) . ' foo' . stubHTTPConnection::END_OF_LINE,
                                                          dechex(3) . stubHTTPConnection::END_OF_LINE,
                                                          dechex(0) . stubHTTPConnection::END_OF_LINE));
        $this->mockSocket->expects($this->exactly(2))
                         ->method('readBinary')
                         ->with($this->equalTo(5))
                         ->will($this->onConsecutiveCalls("foo\r\n", "bar\r\n"));
        $this->assertEquals('foobar', $this->httpResponse->callReadChunked());
        $this->assertFalse($headerList->containsKey('Transfer-Encoding'));
        $this->assertEquals(6, $headerList->get('Content-Length'));
    }
    /**
     * default reading algorithm
     *
     * @test
     */
    public function defaultReading()
    {
        $this->mockSocket->expects($this->once())
                         ->method('read')
                         ->with($this->equalTo(strlen('foobar')))
                         ->will($this->returnValue('foobar'));
        $this->assertEquals('foobar', $this->httpResponse->callReadDefault(strlen('foobar')));
    }

    /**
     * parse head with class info
     *
     * @test
     */
    public function parseHeadClassInfo()
    {
        $head = 'HTTP/1.0 100 Continue';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_0, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('100', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(100, $this->httpResponse->getStatusCode());
        $this->assertEquals('Continue', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_INFO, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with class success
     *
     * @test
     */
    public function parseHeadClassSuccess()
    {
        $head = 'HTTP/1.1 200 OK';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_1, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('200', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(200, $this->httpResponse->getStatusCode());
        $this->assertEquals('OK', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_SUCCESS, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with class redirect
     *
     * @test
     */
    public function parseHeadClassRedirect()
    {
        $head = 'HTTP/1.1 302 Found';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_1, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('302', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(302, $this->httpResponse->getStatusCode());
        $this->assertEquals('Found', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_REDIRECT, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with class client error
     *
     * @test
     */
    public function parseHeadClassClientError()
    {
        $head = 'HTTP/1.1 404 Not Found';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_1, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('404', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(404, $this->httpResponse->getStatusCode());
        $this->assertEquals('Not Found', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_ERROR_CLIENT, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with class server error
     *
     * @test
     */
    public function parseHeadClassServerError()
    {
        $head = 'HTTP/1.1 500 Internal Server Error';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_1, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('500', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(500, $this->httpResponse->getStatusCode());
        $this->assertEquals('Internal Server Error', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_ERROR_SERVER, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with class unknown
     *
     * @test
     */
    public function parseHeadClassUnknown()
    {
        $head = 'HTTP/1.1 600 Illegal Response';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertEquals($head, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertEquals(stubHTTPRequest::VERSION_1_1, $this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertEquals('600', $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(600, $this->httpResponse->getStatusCode());
        $this->assertEquals('Illegal Response', $this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertEquals(stubHTTPResponse::STATUS_CLASS_UNKNOWN, $this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }

    /**
     * parse head with illegal content
     *
     * @test
     */
    public function parseHeadIllegal()
    {
        $head = 'Illegal Response';
        $this->httpResponse->callParseHead($head . stubHTTPConnection::END_OF_LINE);
        $this->assertNull($this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_LINE));
        $this->assertNull($this->httpResponse->getType(stubHTTPResponse::TYPE_HTTP_VERSION));
        $this->assertNull($this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CODE));
        $this->assertEquals(0, $this->httpResponse->getStatusCode());
        $this->assertNull($this->httpResponse->getType(stubHTTPResponse::TYPE_REASON_PHRASE));
        $this->assertNull($this->httpResponse->getType(stubHTTPResponse::TYPE_STATUS_CLASS));
    }
}
?>