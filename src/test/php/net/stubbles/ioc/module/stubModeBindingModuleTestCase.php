<?php
/**
 * Test for net::stubbles::ioc::module::stubModeBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 * @version     $Id: stubModeBindingModuleTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubModeBindingModule');
/**
 * Test for net::stubbles::ioc::module::stubModeBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 * @group       ioc
 * @group       ioc_module
 */
class stubModeBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked mode instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMode;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockMode = $this->getMock('stubMode');
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        $projectPath = '/tmp';
        $this->mockMode->expects($this->once())
                       ->method('registerErrorHandler')
                       ->with($this->equalTo($projectPath));
        $this->mockMode->expects($this->once())
                       ->method('registerExceptionHandler')
                       ->with($this->equalTo($projectPath));
        $modeBindingModule = new stubModeBindingModule($this->mockMode, $projectPath);
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function registerMethodsShouldBeCalledWithBootStrapProjectPath()
    {
        $projectPath = stubBootstrap::getCurrentProjectPath();
        $this->mockMode->expects($this->once())
                       ->method('registerErrorHandler')
                       ->with($this->equalTo($projectPath));
        $this->mockMode->expects($this->once())
                       ->method('registerExceptionHandler')
                       ->with($this->equalTo($projectPath));
        $modeBindingModule = new stubModeBindingModule($this->mockMode);
    }

    /**
     * mode should be bound
     *
     * @test
     */
    public function modeShouldBeBound()
    {
        $modeBindingModule = new stubModeBindingModule($this->mockMode);
        $injector = new stubInjector();
        $modeBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubMode'));
        $this->assertSame($this->mockMode, $injector->getInstance('stubMode'));
    }

    /**
     * no mode given defaults to prod mode
     *
     * @test
     */
    public function noModeGivenDefaultsToProdMode()
    {
        $modeBindingModule = new stubModeBindingModule();
        $injector          = new stubInjector();
        $modeBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasExplicitBinding('stubMode'));
        $this->assertEquals('PROD', $injector->getInstance('stubMode')->name());
        restore_error_handler();
        restore_exception_handler();
    }
}
?>