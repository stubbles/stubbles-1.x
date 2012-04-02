<?php
/**
 * Test for net::stubbles::console::ioc::stubConsoleBindingModule.
 *
 * @package     stubbles
 * @subpackage  console_ioc_test
 */
stubClassLoader::load('net::stubbles::console::ioc::stubConsoleBindingModule');
/**
 * Test for net::stubbles::console::ioc::stubConsoleBindingModule.
 *
 * @package     stubbles
 * @subpackage  console_ioc_test
 * @group       console
 * @group       console_ioc
 */
class stubConsoleBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubConsoleBindingModule
     */
    protected $consoleBindingModule;
    /**
     * binder instance
     *
     * @var  stubBinder
     */
    protected $binder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->consoleBindingModule = new stubConsoleBindingModule();
        $this->binder               = new stubBinder();
    }

    /**
     * helper method
     *
     * @return  stubInjector
     */
    protected function configure()
    {
        $this->consoleBindingModule->configure($this->binder);
        return $this->binder->getInjector();
    }

    /**
     * bindings are condigured
     *
     * @test
     */
    public function bindingsConfigured()
    {
        $injector = $this->configure();
        $this->assertTrue($injector->hasBinding('stubInputStream', 'stdin'));
        $this->assertTrue($injector->hasBinding('stubOutputStream', 'stdout'));
        $this->assertTrue($injector->hasBinding('stubOutputStream', 'stderr'));
        $this->assertTrue($injector->hasBinding('stubExecutor'));
        $this->assertInstanceOf('stubInputStream', $injector->getInstance('stubInputStream', 'stdin'));
        $this->assertSame($injector->getInstance('stubInputStream', 'stdin'), $injector->getInstance('stubInputStream', 'stdin'));
        $this->assertInstanceOf('stubOutputStream', $injector->getInstance('stubOutputStream', 'stdout'));
        $this->assertSame($injector->getInstance('stubOutputStream', 'stdout'), $injector->getInstance('stubOutputStream', 'stdout'));
        $this->assertInstanceOf('stubOutputStream', $injector->getInstance('stubOutputStream', 'stderr'));
        $this->assertSame($injector->getInstance('stubOutputStream', 'stderr'), $injector->getInstance('stubOutputStream', 'stderr'));
        $this->assertInstanceOf('stubExecutor', $injector->getInstance('stubExecutor'));
    }

    /**
     * input stream is always the same instance
     *
     * @test
     */
    public function inputStreamIsSingleton()
    {
        $injector = $this->configure();
        $this->assertSame($injector->getInstance('stubInputStream', 'stdin'), $injector->getInstance('stubInputStream', 'stdin'));
    }

    /**
     * output stream is always the same instance
     *
     * @test
     */
    public function outputStreamIsSingleton()
    {
        $injector = $this->configure();
        $this->assertSame($injector->getInstance('stubOutputStream', 'stdout'), $injector->getInstance('stubOutputStream', 'stdout'));
        $this->assertSame($injector->getInstance('stubOutputStream', 'stderr'), $injector->getInstance('stubOutputStream', 'stderr'));
    }

    /**
     * error stream is always the same instance
     *
     * @test
     */
    public function errorStreamIsSingleton()
    {
        $injector = $this->configure();
        $this->assertSame($injector->getInstance('stubOutputStream', 'stderr'), $injector->getInstance('stubOutputStream', 'stderr'));
    }
}
?>