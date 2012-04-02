<?php
/**
 * Test for net::stubbles::peer::http::stubHTTPConnection.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @version     $Id: stubHTTPConnectionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::peer::http::stubHTTPConnection');
/**
 * Test for net::stubbles::peer::http::stubHTTPConnection.
 *
 * @package     stubbles
 * @subpackage  peer_http_test
 * @group       peer
 * @group       peer_http
 */
class stubHTTPConnectionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be tested
     *
     * @var  stubHTTPConnection
     */
    protected $httpConnection;
    /**
     * URL instance to be used
     *
     * @var  stubHTTPURL
     */
    protected $httpURL;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->httpURL        = stubHTTPURL::fromString('http://example.com/');
        $this->httpConnection = $this->getMock('stubHTTPConnection',
                                               array('createRequest'),
                                               array($this->httpURL)
                                );
    }

    /**
     * assure that a header list instance is passed
     *
     * @test
     */
    public function headerListIsPassed()
    {
        $headerList     = new stubHeaderList();
        $httpConnection = new stubHTTPConnection($this->httpURL, $headerList);
        $this->assertSame($headerList, $httpConnection->getHeaderList());
    }

    /**
     * assure that a header list instance is created
     *
     * @test
     */
    public function headerListIsCreated()
    {
        $this->assertInstanceOf('stubHeaderList', $this->httpConnection->getHeaderList());
    }

    /**
     * all headers should be set using the fluent interface
     *
     * @test
     */
    public function usingFluentInterface()
    {
        $this->assertSame($this->httpConnection, $this->httpConnection->timeout(2)
                                                                      ->asUserAgent('user agent')
                                                                      ->referedFrom('http://example.com/')
                                                                      ->withCookie(array('foo' => 'bar baz'))
                                                                      ->authorizedAs('user', 'pass')
                                                                      ->usingHeader('X-Header', 'value')
        );
        $this->assertEquals(2, $this->httpConnection->getTimeout());
        $headerList = $this->httpConnection->getHeaderList();
        $this->assertEquals('user agent', $headerList->get('User-Agent'));
        $this->assertEquals('http://example.com/', $headerList->get('Referer'));
        $this->assertEquals('foo=bar+baz;', $headerList->get('Cookie'));
        $this->assertEquals('BASIC ' . base64_encode('user:pass'), $headerList->get('Authorization'));
        $this->assertEquals('value', $headerList->get('X-Header'));
    }

    /**
     * initializing a get request should return a response object
     *
     * @test
     */
    public function initializeGetRequest()
    {
        $mockHTTPRequest = $this->getMock('stubHTTPRequest',
                                          array(),
                                          array($this->httpURL, new stubHeaderList(), 8)
                           );
        $this->httpConnection->expects($this->once())
                             ->method('createRequest')
                             ->will($this->returnValue($mockHTTPRequest));
        $mockHTTPRequest->expects($this->never())
                        ->method('preparePost');
        $mockHTTPResponse = $this->getMock('stubHTTPResponse',
                                           array(),
                                           array($this->getMock('stubSocket', array(), array('example.com')))
                            );
        $mockHTTPRequest->expects($this->once())
                        ->method('get')
                        ->with(stubHTTPRequest::VERSION_1_1)
                        ->will($this->returnValue($mockHTTPResponse));
        $this->assertSame($mockHTTPResponse, $this->httpConnection->get(stubHTTPRequest::VERSION_1_1));
    }

    /**
     * initializing a head request should return a response object
     *
     * @test
     */
    public function initializeHeadRequest()
    {
        $mockHTTPRequest = $this->getMock('stubHTTPRequest',
                                          array(),
                                          array($this->httpURL, new stubHeaderList(), 8)
                           );
        $this->httpConnection->expects($this->once())
                             ->method('createRequest')
                             ->will($this->returnValue($mockHTTPRequest));
        $mockHTTPRequest->expects($this->never())
                        ->method('preparePost');
        $mockHTTPResponse = $this->getMock('stubHTTPResponse',
                                           array(),
                                           array($this->getMock('stubSocket', array(), array('example.com')))
                            );
        $mockHTTPRequest->expects($this->once())
                        ->method('head')
                        ->with(stubHTTPRequest::VERSION_1_1)
                        ->will($this->returnValue($mockHTTPResponse));
        $this->assertSame($mockHTTPResponse, $this->httpConnection->head(stubHTTPRequest::VERSION_1_1));
    }

    /**
     * initializing a post request should return a response object
     *
     * @test
     */
    public function initializePostRequest()
    {
        $mockHTTPRequest = $this->getMock('stubHTTPRequest',
                                          array(),
                                          array($this->httpURL, new stubHeaderList(), 8)
                           );
        $this->httpConnection->expects($this->once())
                             ->method('createRequest')
                             ->will($this->returnValue($mockHTTPRequest));
        $mockHTTPRequest->expects($this->once())
                        ->method('preparePost')
                        ->with($this->equalTo(array('foo' => 'bar')))
                        ->will($this->returnValue($mockHTTPRequest));
        $mockHTTPResponse = $this->getMock('stubHTTPResponse',
                                           array(),
                                           array($this->getMock('stubSocket', array(), array('example.com')))
                            );
        $mockHTTPRequest->expects($this->once())
                        ->method('post')
                        ->with(stubHTTPRequest::VERSION_1_1)
                        ->will($this->returnValue($mockHTTPResponse));
        $this->assertSame($mockHTTPResponse, $this->httpConnection->post(array('foo' => 'bar'), stubHTTPRequest::VERSION_1_1));
    }
}
?>