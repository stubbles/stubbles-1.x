<?php
/**
 * Test for net::stubbles::util::log::ioc::stubLogBindingModule.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @version     $Id: stubLogBindingModuleTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::ioc::stubLogBindingModule');
/**
 * Test for net::stubbles::util::log::ioc::stubLogBindingModule.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @group       util_log
 * @group       util_log_ioc
 */
class stubLogBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLogBindingModule
     */
    protected $logBindingModule;
    /**
     * mocked log entry factory
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->injector         = new stubInjector();
        $this->logBindingModule = stubLogBindingModule::create(dirname(__FILE__));
        $this->logBindingModule->configure(new stubBinder($this->injector));
    }

    /**
     * log path should be bound correctly
     *
     * @test
     */
    public function logPathIsIsNotBoundByDefault()
    {
        $injector               = new stubInjector();
        $this->logBindingModule = stubLogBindingModule::create();
        $this->logBindingModule->configure(new stubBinder($injector));
        $this->assertFalse($injector->hasBinding(stubConstantBinding::TYPE, 'net.stubbles.log.path'));
    }

    /**
     * log path should be bound correctly
     *
     * @test
     */
    public function logPathIsCorrectlyBoundWhenSet()
    {
        $this->assertSame(dirname(__FILE__), $this->injector->getInstance(stubConstantBinding::TYPE, 'net.stubbles.log.path'));
    }

    /**
     * stubLogEntryFactory should be bound to stubDefaultLogEntryFactory as a singleton
     *
     * @test
     */
    public function logEntryFactoryClassIsCorrectlyBound()
    {
        $logEntryFactory = $this->injector->getInstance('stubLogEntryFactory');
        $this->assertInstanceOf('stubDefaultLogEntryFactory', $logEntryFactory);
        $this->assertSame($logEntryFactory, $this->injector->getInstance('stubLogEntryFactory'));
    }

    /**
     * stubLogEntryFactory should be bound to given log entry factory class as a singleton
     *
     * @test
     */
    public function otherLogEntryFactoryClassIsCorrectlyBound()
    {
        $logBindingModule = stubLogBindingModule::create(dirname(__FILE__))
                                                ->setLogEntryFactoryClassName('net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory');
        $logBindingModule->configure(new stubBinder($this->injector));
        $logEntryFactory = $this->injector->getInstance('stubLogEntryFactory');
        $this->assertInstanceOf('stubEmptyLogEntryFactory', $logEntryFactory);
        $this->assertSame($logEntryFactory, $this->injector->getInstance('stubLogEntryFactory'));
    }

    /**
     * base logger class should be bound
     *
     * @test
     */
    public function baseLoggerIsCorrectlyBound()
    {
        $logger = $this->injector->getInstance('stubLogger', 'util.log.baseLogger');
        $this->assertInstanceOf('stubLogger', $logger);
        $this->assertNotSame($logger, $this->injector->getInstance('stubLogger', 'util.log.baseLogger'));
    }

    /**
     * other logger class should be bound
     *
     * @test
     */
    public function otherLoggerIsCorrectlyBound()
    {
        $logger = $this->injector->getInstance('stubLogger');
        $this->assertInstanceOf('stubLogger', $logger);
        $this->assertSame($logger, $this->injector->getInstance('stubLogger'));
        $this->assertSame($logger, $this->injector->getInstance('stubLogger', stubLogger::LEVEL_DEBUG));
        $this->assertSame($logger, $this->injector->getInstance('stubLogger', stubLogger::LEVEL_ERROR));
        $this->assertSame($logger, $this->injector->getInstance('stubLogger', stubLogger::LEVEL_INFO));
        $this->assertSame($logger, $this->injector->getInstance('stubLogger', stubLogger::LEVEL_WARN));
        $this->assertSame($logger, $this->injector->getInstance('stubLogger', stubLogger::LEVEL_ALL));
    }
}
?>