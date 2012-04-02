<?php
/**
 * Tests for net::stubbles::ipo::session::stubNoneDurableSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @version     $Id: stubNoneDurableSessionTestCase.php 2886 2011-01-11 22:00:42Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::session::stubNoneDurableSession');
/**
 * Tests for net::stubbles::ipo::session::stubNoneDurableSession.
 *
 * @package     stubbles
 * @subpackage  ipo_session_test
 * @group       ipo
 * @group       ipo_session
 */
class stubNoneDurableSessionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubNoneDurableSession
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
        $this->session     = new stubNoneDurableSession($this->mockRequest, $this->response, 'test');
    }

    /**
     * @test
     */
    public function regenerateId()
    {
        $id = $this->session->getId();
        $this->assertSame($this->session, $this->session->regenerateId());
        $this->assertNotEquals($id, $this->session->getId());
    }

    /**
     * @test
     */
    public function regenerateIdWithGivenSessionId()
    {
        $id = $this->session->getId();
        $this->assertSame($this->session, $this->session->regenerateId('foo'));
        $this->assertNotEquals($id, $this->session->getId());
        $this->assertEquals('foo', $this->session->getId());
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