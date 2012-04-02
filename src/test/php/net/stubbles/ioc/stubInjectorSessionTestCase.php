<?php
/**
 * Test for net::stubbles::ioc::stubInjector with the singleton scope.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorSessionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder',
                      'net::stubbles::ipo::session::stubNoneDurableSession'
);
/**
 * Interface to be used in the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
interface stubInjectorSessionTestCase_Number
{
    public function display();
}
/**
 * Class to be used in the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorSessionTestCase_Answer implements stubInjectorSessionTestCase_Number
{
    public function display()
    {
        echo 42;
    }
}
/**
 * Test for net::stubbles::ioc::stubInjector with the session scope.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorSessionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * binder instance to be used in tests
     *
     * @var  stubBinder
     */
    protected $binder;
    /**
     * mocked session instance
     *
     * @var  
     */
    protected $nonDurableSession;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->binder            = new stubBinder();
        $this->nonDurableSession = new stubNoneDurableSession($this->getMock('stubRequest'), $this->getMock('stubResponse'), 'sessionName');
        $this->binder->setSessionForSessionScope($this->nonDurableSession);
    }

    /**
     * @test
     */
    public function storesCreatedInstanceInSession()
    {
        $this->binder->bind('stubInjectorSessionTestCase_Number')
                     ->to('stubInjectorSessionTestCase_Answer')
                     ->inSession();
        $injector = $this->binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorSessionTestCase_Number'));

        $number = $injector->getInstance('stubInjectorSessionTestCase_Number');
        $this->assertInstanceOf('stubInjectorSessionTestCase_Number', $number);
        $this->assertInstanceOf('stubInjectorSessionTestCase_Answer', $number);
        $this->assertTrue($this->nonDurableSession->hasValue(stubBindingScopeSession::SESSION_KEY . 'stubInjectorSessionTestCase_Answer'));
        $this->assertSame($number, $this->nonDurableSession->getValue(stubBindingScopeSession::SESSION_KEY . 'stubInjectorSessionTestCase_Answer'));
        $this->assertSame($number, $injector->getInstance('stubInjectorSessionTestCase_Number'));
    }

    /**
     * @test
     */
    public function usesInstanceFromSessionIfAvailable()
    {
        $this->binder->bind('stubInjectorSessionTestCase_Number')
                     ->to('stubInjectorSessionTestCase_Answer')
                     ->inSession();
        $injector = $this->binder->getInjector();
        $number   = new stubInjectorSessionTestCase_Answer();
        $this->nonDurableSession->putValue(stubBindingScopeSession::SESSION_KEY . 'stubInjectorSessionTestCase_Answer', $number);
        $this->assertTrue($injector->hasBinding('stubInjectorSessionTestCase_Number'));
        $this->assertSame($number, $injector->getInstance('stubInjectorSessionTestCase_Number'));
    }

    /**
     * @test
     * @expectedException  stubRuntimeException
     */
    public function noSessionAvailableThrowsRuntimeException()
    {
        $scope = new stubBindingScopeSession();
        $scope->getInstance($this->getMock('stubBaseReflectionClass'),
                            $this->getMock('stubBaseReflectionClass'),
                            $this->getMock('stubInjectionProvider')
        );
    }
}
?>