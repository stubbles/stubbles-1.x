<?php
/**
 * Tests for net::stubbles::ipo::session::stubFallbackSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @version     $Id: stubFallbackSessionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubFallbackSession');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 */
class TeststubFallbackSession extends stubFallbackSession
{
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    public static $mockSession;

    /**
     * creates the decorated and possibly exception-throwing session instance
     *
     * @param   stubRequest   $request      request instance
     * @param   stubResponse  $response     response instance
     * @param   string        $sessionName  name of the session
     * @return  stubSession
     */
    protected function doConstruct(stubRequest $request, stubResponse $response, $sessionName)
    {
        if (null !== self::$mockSession) {
            return self::$mockSession;
        }
        
        throw new stubException('failure');
    }

    /**
     * returns created session instance
     *
     * @return  stubSession
     */
    public function getSession()
    {
        return $this->session;
    }
}
/**
 * Tests for net::stubbles::ipo::session::stubFallbackSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @group       ipo
 * @group       ipo_session
 */
class stubFallbackSessionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubFallbackSession
     */
    protected $fallbackSession;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockSession = $this->getMock('stubSession');
        TeststubFallbackSession::$mockSession = $this->mockSession;
        $this->fallbackSession = new TeststubFallbackSession($this->getMock('stubRequest'),
                                                             $this->getMock('stubResponse'),
                                                             'test'
                                 );
    }

    /**
     * session id from request should be used
     *
     * @test
     */
    public function fallbackToNoneDurableSession()
    {
        TeststubFallbackSession::$mockSession = null;
        $fallbackSession = new TeststubFallbackSession($this->getMock('stubRequest'),
                                                       $this->getMock('stubResponse'),
                                                       'test'
                           );
        $this->assertInstanceOf('stubNoneDurableSession', $fallbackSession->getSession());
    }

    /**
     * session id from cookie should be used
     *
     * @test
     */
    public function originalSessionInstanceCreated()
    {
        $this->assertSame($this->mockSession, $this->fallbackSession->getSession());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function isNewOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('isNew')
                          ->will($this->returnValue(true));
        $this->assertTrue($this->fallbackSession->isNew());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getStartTimeOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getStartTime')
                          ->will($this->returnValue(1000));
        $this->assertEquals(1000, $this->fallbackSession->getStartTime());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getIdOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getId')
                          ->will($this->returnValue(313));
        $this->assertEquals(313, $this->fallbackSession->getId());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getNameOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getName')
                          ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->fallbackSession->getName());
    }

    /**
     * @test
     */
    public function regenerateIdOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('regenerateId')
                          ->with($this->equalTo(null));
        $this->assertSame($this->fallbackSession,
                          $this->fallbackSession->regenerateId()
        );
    }

    /**
     * @test
     */
    public function regenerateIdOfDecoratedSessionIsCalledWithGivenSessionId()
    {
        $this->mockSession->expects($this->once())
                          ->method('regenerateId')
                          ->with($this->equalTo('foo'));
        $this->assertSame($this->fallbackSession,
                          $this->fallbackSession->regenerateId('foo')
        );
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getCurrentTokenOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getCurrentToken')
                          ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->fallbackSession->getCurrentToken());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getNextTokenOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getNextToken')
                          ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->fallbackSession->getNextToken());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function isValidOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('isValid')
                          ->will($this->returnValue(true));
        $this->assertTrue($this->fallbackSession->isValid());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function invalidateOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('invalidate');
        $this->fallbackSession->invalidate();
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function resetOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('reset')
                          ->will($this->returnValue(5));
        $this->assertEquals(5, $this->fallbackSession->reset());
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function putValueOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('putValue')
                          ->with($this->equalTo('foo'), $this->equalTo('bar'));
        $this->fallbackSession->putValue('foo', 'bar');
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getValueOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->at(0))
                          ->method('getValue')
                          ->with($this->equalTo('foo'), $this->equalTo(null))
                          ->will($this->returnValue('bar'));
        $this->mockSession->expects($this->at(1))
                          ->method('getValue')
                          ->with($this->equalTo('foo'), $this->equalTo('bar'))
                          ->will($this->returnValue('baz'));
        $this->assertEquals('bar', $this->fallbackSession->getValue('foo'));
        $this->assertEquals('baz', $this->fallbackSession->getValue('foo', 'bar'));
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function hasValueOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('hasValue')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue(true));
        $this->assertTrue($this->fallbackSession->hasValue('foo'));
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function removeValueOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('removeValue')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue(true));
        $this->assertTrue($this->fallbackSession->removeValue('foo'));
    }

    /**
     * decorated session instance will be called and its return value returned
     *
     * @test
     */
    public function getValueKeysOfDecoratedSessionIsCalled()
    {
        $this->mockSession->expects($this->once())
                          ->method('getValueKeys')
                          ->will($this->returnValue(array('foo', 'baz')));
        $this->assertEquals(array('foo', 'baz'), $this->fallbackSession->getValueKeys());
    }
}
?>