<?php
/**
 * Tests for net::stubbles::ipo::session::stubNoneStoringSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @version     $Id: stubNoneStoringSessionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubNoneStoringSession');
/**
 * Tests for net::stubbles::ipo::session::stubNoneStoringSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @group       ipo
 * @group       ipo_session
 */
class stubNoneStoringSessionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubNoneStoringSession
     */
    protected $session;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * response instance
     *
     * @var  stubResponse
     */
    protected $response;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest = $this->getMock('stubRequest');
        $this->response    = new stubBaseResponse();
        $this->session     = new stubNoneStoringSession($this->mockRequest, $this->response, 'test');
    }

    /**
     * session id from request should be used
     *
     * @test
     */
    public function useSessionIdFromRequest()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->with($this->equalTo('test'))
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('readParam')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'test',
                                                                                  '12345678901234567890123456789012'
                                                    )
                                 )
                            );
        $session = new stubNoneStoringSession($this->mockRequest, $this->response, 'test');
        $this->assertEquals('12345678901234567890123456789012', $session->getId());
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertEquals('12345678901234567890123456789012', $cookies['test']->getValue());
        $this->assertTrue($cookies['test']->isHttpOnly());
        $this->assertFalse($session->isNew());
    }

    /**
     * session id from cookie should be used
     *
     * @test
     */
    public function useSessionIdFromCookie()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(false));
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(true));
        $this->mockRequest->expects($this->once())
                          ->method('readCookie')
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'test',
                                                                                  '12345678901234567890123456789012'
                                                    )
                                 )
                            );
        $session = new stubNoneStoringSession($this->mockRequest, $this->response, 'test');
        $this->assertEquals('12345678901234567890123456789012', $session->getId());
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertEquals('12345678901234567890123456789012', $cookies['test']->getValue());
        $this->assertTrue($cookies['test']->isHttpOnly());
        $this->assertFalse($session->isNew());
    }

    /**
     * new session id should be generated
     *
     * @test
     */
    public function generateNewSessionId()
    {
        $this->mockRequest->expects($this->once())
                          ->method('hasParam')
                          ->will($this->returnValue(false));
        $this->mockRequest->expects($this->once())
                          ->method('hasCookie')
                          ->will($this->returnValue(false));
        $this->mockRequest->expects($this->never())
                          ->method('getValidatedValue');
        $session = new stubNoneStoringSession($this->mockRequest, $this->response, 'test');
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertEquals($session->getId(), $cookies['test']->getValue());
        $this->assertTrue($session->isNew());
    }

    /**
     * @test
     */
    public function regenerateId()
    {
        $id = $this->session->getId();
        $this->assertSame($this->session, $this->session->regenerateId());
        $this->assertNotEquals($id, $this->session->getId());
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertNotEquals($id, $cookies['test']->getValue());
        $this->assertEquals($this->session->getId(), $cookies['test']->getValue());
    }

    /**
     * @test
     */
    public function regenerateIdWithGivenSessionId()
    {
        $id = $this->session->getId();
        $this->assertSame($this->session, $this->session->regenerateId('foo'));
        $this->assertNotEquals($id, $this->session->getId());
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertNotEquals($id, $cookies['test']->getValue());
        $this->assertEquals('foo', $cookies['test']->getValue());
        $this->assertEquals($this->session->getId(), $cookies['test']->getValue());
    }

    /**
     * test that invalidating the session makes it invalid
     *
     * @test
     */
    public function invalidate()
    {
        $this->session->invalidate();
        $this->assertFalse($this->session->isValid());
        $cookies = $this->response->getCookies();
        $this->assertInstanceOf('stubCookie', $cookies['test']);
        $this->assertEquals('test', $cookies['test']->getName());
        $this->assertEquals($this->session->getId(), $cookies['test']->getValue());
    }

    /**
     * test getting a value from session
     *
     * @test
     */
    public function putGetHasValue()
    {
        $this->assertNull($this->session->getValue('foo'));
        $this->assertEquals('bar', $this->session->getValue('foo', 'bar'));
        $this->assertFalse($this->session->hasValue('foo'));
        $this->session->putValue('foo', 'baz');
        $this->assertTrue($this->session->hasValue('foo'));
        $this->assertEquals('baz', $this->session->getValue('foo'));
        $this->assertEquals('baz', $this->session->getValue('foo', 'bar'));
    }

    /**
     * test removing a value from session
     *
     * @test
     */
    public function removeValue()
    {
        $this->assertFalse($this->session->removeValue('foo'));
        $this->session->putValue('foo', 'baz');
        $this->assertTrue($this->session->hasValue('foo'));
        $this->assertTrue($this->session->removeValue('foo'));
        $this->assertFalse($this->session->hasValue('foo'));
        $this->assertFalse($this->session->removeValue('foo'));
    }

    /**
     * @test
     */
    public function hasNoValueKeysByDefault()
    {
        $this->assertEquals(array(),
                            $this->session->getValueKeys()
        );
    }

    /**
     * @test
     */
    public function getValueKeysReturnsKeysOfAddedValues()
    {
        $this->session->putValue('foo', 'baz');
        $this->assertEquals(array('foo'),
                            $this->session->getValueKeys()
        );
    }
}
?>