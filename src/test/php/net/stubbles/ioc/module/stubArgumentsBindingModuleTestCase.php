<?php
/**
 * Test for net::stubbles::ioc::module::stubArgumentsBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 */
stubClassLoader::load('net::stubbles::ioc::module::stubArgumentsBindingModule');
/**
 * Test for net::stubbles::ioc::module::stubArgumentsBindingModule.
 *
 * @package     stubbles
 * @subpackage  ioc_module_test
 * @group       ioc
 * @group       ioc_module
 */
class stubArgumentsBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function argumentsAreBound()
    {
        $injector               = new stubInjector();
        $argumentsBindingModule = new stubArgumentsBindingModule(array('foo', 'bar', 'baz'));
        $argumentsBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasConstant('argv.0'));
        $this->assertTrue($injector->hasConstant('argv.1'));
        $this->assertTrue($injector->hasConstant('argv.2'));
        $this->assertEquals('foo', $injector->getConstant('argv.0'));
        $this->assertEquals('bar', $injector->getConstant('argv.1'));
        $this->assertEquals('baz', $injector->getConstant('argv.2'));
    }
}
?>