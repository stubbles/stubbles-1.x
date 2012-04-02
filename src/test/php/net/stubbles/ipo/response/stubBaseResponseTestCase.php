<?php
/**
 * Tests for net::stubbles::ipo::response::stubBaseResponse.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @version     $Id: stubBaseResponseTestCase.php 3106 2011-03-23 17:44:53Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubBaseResponse');
/**
 * Tests for net::stubbles::ipo::response::stubBaseResponse.
 *
 * @package     stubbles
 * @subpackage  ipo_response_test
 * @group       ipo
 * @group       ipo_response
 */
class stubBaseResponseTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubBaseResponse
     */
    protected $response;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->response = $this->getMock('stubBaseResponse', array('header', 'sendBody'));
    }

    /**
     * @test
     */
    public function versionIs1_1ByDefault()
    {
        $this->assertEquals('1.1', $this->response->getVersion());
    }

    /**
     * @test
     */
    public function versionCanBeSetOnConstruction()
    {
        $response = new stubBaseResponse('1.0');
        $this->assertEquals('1.0', $response->getVersion());
    }

    /**
     * @test
     * @deprecated  will be removed with 1.8 or 2.0
     */
    public function versionCanBeSetTo1_0()
    {
        $this->assertEquals('1.0',
                            $this->response->setVersion('1.0')
                                           ->getVersion()
        );
    }

    /**
     * @test
     */
    public function clearingResponseDoesNotResetVersion()
    {
        $response = new stubBaseResponse('1.0');
        $this->assertEquals('1.0',
                            $response->clear()
                                     ->getVersion()
        );
    }

    /**
     * @test
     */
    public function hasStatusCode200ByDefault()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @group  bug251
     * @since  1.5.0
     */
    public function settingStatusCodeToInvalidValueThrowsIllegalArgumentException()
    {
        $this->response->setStatusCode(313);
    }

    /**
     * @test
     */
    public function statusCodeSetToNullWillNotSendStatusCodeHeaderLine()
    {
        $this->assertNull($this->response->setStatusCode(null)->getStatusCode());
        $this->response->expects($this->never())->method('header');
        $this->response->send();
    }

    /**
     * @test
     */
    public function clearingResponseResetsStatusCodeTo200()
    {
        $this->assertEquals(200,
                            $this->response->setStatusCode(500)
                                           ->clear(500)
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     */
    public function statusCodeInCgiSapi()
    {
        $this->response = $this->getMock('stubBaseResponse', array('header', 'sendBody'), array('1.1', 'cgi'));
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->response->expects($this->once())->method('header')->with($this->equalTo('Status: 200 OK'));
        $this->response->send();
    }

    /**
     * @test
     */
    public function statusCodeChangedInCgiSapi()
    {
        $this->response = $this->getMock('stubBaseResponse', array('header', 'sendBody'), array('1.1', 'cgi'));
        $this->response->setStatusCode(404, 'Not Found');
        $this->response->expects($this->once())->method('header')->with($this->equalTo('Status: 404 Not Found'));
        $this->assertEquals(200,
                            $this->response->send()
                                           ->clear()
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     * @since  1.5.0
     * @group  bug251
     */
    public function statusCodeChangedWithoutReasonPhradeInCgiSapi()
    {
        $this->response = $this->getMock('stubBaseResponse', array('header', 'sendBody'), array('1.1', 'cgi'));
        $this->response->setStatusCode(404);
        $this->response->expects($this->once())->method('header')->with($this->equalTo('Status: 404 Not Found'));
        $this->assertEquals(200,
                            $this->response->send()
                                           ->clear()
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     */
    public function statusCodeInOtherSapi()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
        $this->response->expects($this->once())->method('header')->with($this->equalTo('HTTP/1.1 200 OK'));
        $this->response->send();
    }

    /**
     * @test
     */
    public function statusCodeChangedInOtherSapi()
    {
        $this->assertEquals(404,
                            $this->response->setStatusCode(404, 'Not Found')
                                           ->getStatusCode()
        );
        $this->response->expects($this->once())->method('header')->with($this->equalTo('HTTP/1.1 404 Not Found'));
        $this->assertEquals(200,
                            $this->response->send()
                                           ->clear()
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     * @since  1.5.0
     * @group  bug251
     */
    public function statusCodeChangedWithoutReasonPhradeInOtherSapi()
    {
        $this->assertEquals(404,
                            $this->response->setStatusCode(404)
                                           ->getStatusCode()
        );
        $this->response->expects($this->once())->method('header')->with($this->equalTo('HTTP/1.1 404 Not Found'));
        $this->assertEquals(200,
                            $this->response->send()
                                           ->clear()
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     */
    public function hasNoHeadersByDefault()
    {
        $this->assertEquals(array(), $this->response->getHeaders());
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function checkForNonExistingHeaderReturnsFalse()
    {
        $this->assertFalse($this->response->hasHeader('doesNotExist'));
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function retrieveNonExistingHeaderReturnsNull()
    {
        $this->assertNull($this->response->getHeader('doesNotExist'));
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function checkForExistingHeaderReturnsTrue()
    {
        $this->response->addHeader('name', 'value1');
        $this->assertTrue($this->response->hasHeader('name'));
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function retrieveExistingHeaderReturnsValueOfHeader()
    {
        $this->response->addHeader('name', 'value1');
        $this->assertEquals('value1', $this->response->getHeader('name'));
    }

    /**
     * @test
     */
    public function addedHeadersAreSend()
    {
        $this->response->setStatusCode(null);
        $this->assertEquals(array('name' => 'value1'),
                            $this->response->addHeader('name', 'value1')
                                           ->getHeaders()
        );
        $this->response->expects($this->once())->method('header')->with($this->equalTo('name: value1'));
        $this->response->send();
    }

    /**
     * @test
     */
    public function addingHeaderWithSameNameReplacesExistingHeader()
    {
        $this->response->setStatusCode(null);
        $this->assertEquals(array('name' => 'value2'),
                            $this->response->addHeader('name', 'value1')
                                           ->addHeader('name', 'value2')
                                           ->getHeaders()
        );
        $this->response->expects($this->once())->method('header')->with($this->equalTo('name: value2'));
        $this->response->send();
    }

    /**
     * @test
     */
    public function clearingResponseRemovesAllHeaders()
    {
        $this->assertEquals(array(),
                            $this->response->addHeader('name', 'value1')
                                           ->clear()
                                           ->getHeaders()
        );
    }

    /**
     * @test
     */
    public function hasNoCookiesByDefault()
    {
        $this->assertEquals(array(), $this->response->getCookies());
    }

    /**
     * @test
     */
    public function cookiesAreSend()
    {
        $this->response->setStatusCode(null);
        $mockCookie = $this->getMock('stubCookie', array(), array('foo', 'bar'));
        $mockCookie->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $this->assertEquals(array('foo' => $mockCookie),
                            $this->response->addCookie($mockCookie)
                                           ->getCookies()
        );
        $mockCookie->expects($this->once())->method('send');
        $this->response->send();
    }

    /**
     * @test
     */
    public function addingCookieWithSameNameReplacesExistingCookie()
    {
        $this->response->setStatusCode(null);
        $mockCookie = $this->getMock('stubCookie', array(), array('foo', 'bar'));
        $mockCookie->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $this->assertEquals(array('foo' => $mockCookie),
                            $this->response->addCookie($mockCookie)
                                           ->addCookie($mockCookie)
                                           ->getCookies()
        );
        $mockCookie->expects($this->once())->method('send');
        $this->response->send();
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function checkForNonExistingCookieReturnsFalse()
    {
        $this->assertFalse($this->response->hasCookie('doesNotExist'));
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function retrieveNonExistingCookieReturnsNull()
    {
        $this->assertNull($this->response->getCookie('doesNotExist'));
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function checkForExistingCookieReturnsTrue()
    {
        $mockCookie = $this->getMock('stubCookie', array(), array('foo', 'bar'));
        $mockCookie->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $this->assertTrue($this->response->addCookie($mockCookie)
                                         ->hasCookie('foo')
        );
    }

    /**
     * @test
     * @group  bug253
     * @since  1.5.0
     */
    public function retrieveExistingCookieReturnsCookie()
    {
        $mockCookie = $this->getMock('stubCookie', array(), array('foo', 'bar'));
        $mockCookie->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $this->assertSame($mockCookie,
                          $this->response->addCookie($mockCookie)
                                         ->getCookie('foo')
        );
    }

    /**
     * @test
     */
    public function clearingResponseRemovesAllCookies()
    {
        $mockCookie = $this->getMock('stubCookie', array(), array('foo', 'bar'));
        $mockCookie->expects($this->any())->method('getName')->will($this->returnValue('foo'));
        $this->assertEquals(array(),
                            $this->response->addCookie($mockCookie)
                                           ->clear()
                                           ->getCookies()
        );
    }

    /**
     * @test
     */
    public function hasNoBodyByDefault()
    {
        $this->assertNull($this->response->getBody());
    }

    /**
     * @test
     */
    public function replaceBodyRemovesOldBodyCompletely()
    {
        $this->assertEquals('foo', $this->response->write('foo')->getBody());
        $this->assertEquals('bar', $this->response->replaceBody('bar')->getBody());
    }

    /**
     * @test
     */
    public function bodyIsSend()
    {
        $this->response->expects($this->once())
                       ->method('sendBody')
                       ->with($this->equalTo('foo'));
        $this->response->write('foo')
                       ->send();
    }

    /**
     * @test
     */
    public function clearingResponseRemovesBody()
    {
        $this->assertNull($this->response->write('foo')
                                         ->clear()
                                         ->getBody()
        );
    }

    /**
     * @test
     */
    public function doesNotWriteBodyIfNoBodyPresent()
    {
        $this->response->expects($this->never())
                       ->method('sendBody');
        $this->response->send();
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function redirectAddsLocationHeaderAndStatusCode()
    {
        $this->response->expects($this->at(0))
                       ->method('header')
                       ->with($this->equalTo('HTTP/1.1 301 Moved Permanently'));
        $this->response->expects($this->at(1))
                       ->method('header')
                       ->with($this->equalTo('Location: http://example.com/'));
        $this->response->redirect('http://example.com/',
                                                    301,
                                                    'Moved Permanently'
                         )
                       ->send();
    }

    /**
     * @test
     * @group  bug251
     * @since  1.5.0
     */
    public function redirectWithoutReasonPhraseAddsLocationHeaderAndStatusCode()
    {
        $this->response->expects($this->at(0))
                       ->method('header')
                       ->with($this->equalTo('HTTP/1.1 301 Moved Permanently'));
        $this->response->expects($this->at(1))
                       ->method('header')
                       ->with($this->equalTo('Location: http://example.com/'));
        $this->response->redirect('http://example.com/',
                                                    301
                         )
                       ->send();
    }

    /**
     * @test
     * @group  bug251
     * @since  1.5.0
     */
    public function redirectWithoutStatusCodeAndReasonPhraseAddsLocationHeaderAndStatusCode302()
    {
        $this->response->expects($this->at(0))
                       ->method('header')
                       ->with($this->equalTo('HTTP/1.1 302 Found'));
        $this->response->expects($this->at(1))
                       ->method('header')
                       ->with($this->equalTo('Location: http://example.com/'));
        $this->response->redirect('http://example.com/')
                       ->send();
    }

    /**
     * @test
     * @since  1.5.0
     */
    public function sendReturnsItself()
    {
        $this->assertSame($this->response, $this->response->send());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeDoesNotChangeHttpVersion()
    {
        $this->assertEquals('1.1', $this->response->getVersion());
        $responseToMerge = new stubBaseResponse('1.0');
        $this->assertEquals('1.1',
                            $this->response->merge($responseToMerge)
                                           ->getVersion()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeSetsStatusCodeToStatusCodeOfResponseToMerge()
    {
        $this->assertEquals(200, $this->response->getStatusCode());
        $responseToMerge = new stubBaseResponse();
        $responseToMerge->setStatusCode(201);
        $this->assertEquals(201,
                            $this->response->merge($responseToMerge)
                                           ->getStatusCode()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeSetsResponseBodyToBodyIfResponseToMerge()
    {
        $this->assertEquals('foo', $this->response->write('foo')->getBody());
        $responseToMerge = new stubBaseResponse();
        $responseToMerge->write('bar');
        $this->assertEquals('bar',
                            $this->response->merge($responseToMerge)
                                           ->getBody()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeAddsHeadersFromResponseToMerge()
    {
        $this->assertEquals(array(), $this->response->getHeaders());
        $responseToMerge = new stubBaseResponse();
        $responseToMerge->addHeader('foo', 'bar');
        $this->assertEquals(array('foo' => 'bar'),
                            $this->response->merge($responseToMerge)
                                           ->getHeaders()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeOverwritesExistingHeadersWithHeadersFromResponseToMerge()
    {
        $this->assertEquals(array('foo' => 'bar'),
                            $this->response->addHeader('foo', 'bar')
                                           ->getHeaders()
        );
        $responseToMerge = new stubBaseResponse();
        $responseToMerge->addHeader('foo', 'baz');
        $this->assertEquals(array('foo' => 'baz'),
                            $this->response->merge($responseToMerge)
                                           ->getHeaders()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeAddsCookiesFromResponseToMerge()
    {
        $this->assertEquals(array(), $this->response->getCookies());
        $responseToMerge = new stubBaseResponse();
        $cookie = stubCookie::create('foo', 'bar');
        $responseToMerge->addCookie($cookie);
        $this->assertEquals(array('foo' => $cookie),
                            $this->response->merge($responseToMerge)
                                           ->getCookies()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug263
     */
    public function mergeOverwritesExistingCookiesWithCookiesFromResponseToMerge()
    {
        $cookie1 = stubCookie::create('foo', 'bar');
        $this->assertEquals(array('foo' => $cookie1),
                            $this->response->addCookie($cookie1)
                                           ->getCookies()
        );
        $responseToMerge = new stubBaseResponse();
        $cookie2 = stubCookie::create('foo', 'baz');
        $responseToMerge->addCookie($cookie2);
        $this->assertEquals(array('foo' => $cookie2),
                            $this->response->merge($responseToMerge)
                                           ->getCookies()
        );
    }
}
?>