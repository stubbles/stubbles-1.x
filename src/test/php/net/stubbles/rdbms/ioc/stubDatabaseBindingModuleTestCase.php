<?php
/**
 * Test for net::stubbles::rdbms::ioc::stubDatabaseBindingModule.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc_test
 * @version     $Id: stubDatabaseBindingModuleTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::ioc::stubDatabaseBindingModule');
/**
 * Test for net::stubbles::rdbms::ioc::stubDatabaseBindingModule.
 *
 * @package     stubbles
 * @subpackage  rdbms_ioc_test
 * @group       rdbms
 * @group       rdbms_ioc
 */
class stubDatabaseBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * fallback enabled
     *
     * @test
     */
    public function fallbackEnabledDescriptorDisabled()
    {
        $databaseBindingModule = new stubDatabaseBindingModule();
        $injector              = new stubInjector();
        $binder                = new stubBinder($injector);
        $binder->bindConstant()
               ->named('net.stubbles.config.path')
               ->to(TEST_SRC_PATH);
        $databaseBindingModule->configure($binder);
        $this->assertTrue($injector->hasBinding('stubDatabaseInitializer'));
        $this->assertTrue($injector->hasConstant('net.stubbles.rdbms.fallback'));
        $this->assertFalse($injector->hasConstant('net.stubbles.rdbms.descriptor'));
        $this->assertTrue($injector->hasBinding('stubDatabaseConnection'));
        $this->assertInstanceOf('stubPropertyBasedDatabaseInitializer', $injector->getInstance('stubDatabaseInitializer'));
        $this->assertTrue($injector->getConstant('net.stubbles.rdbms.fallback'));
    }

    /**
     * fallback disabled
     *
     * @test
     */
    public function fallbackDisabledDescriptorEnabled()
    {
        $mockDatabaseInitializerClassName        = get_class($this->getMock('stubDatabaseInitializer'));
        $mockDatabaseConnectionProviderClassName = get_class($this->getMock('stubInjectionProvider'));
        $databaseBindingModule = stubDatabaseBindingModule::create(false, 'rdbms-prod')
                                                          ->setDatabaseInitializerClassName($mockDatabaseInitializerClassName)
                                                          ->setDatabaseConnectionProviderClassName($mockDatabaseConnectionProviderClassName);
        $injector              = new stubInjector();
        $databaseBindingModule->configure(new stubBinder($injector));
        $this->assertTrue($injector->hasBinding('stubDatabaseInitializer'));
        $this->assertTrue($injector->hasConstant('net.stubbles.rdbms.fallback'));
        $this->assertTrue($injector->hasConstant('net.stubbles.rdbms.descriptor'));
        $this->assertTrue($injector->hasBinding('stubDatabaseConnection'));
        $this->assertInstanceOf($mockDatabaseInitializerClassName, $injector->getInstance('stubDatabaseInitializer'));
        $this->assertFalse($injector->getConstant('net.stubbles.rdbms.fallback'));
        $this->assertEquals('rdbms-prod', $injector->getConstant('net.stubbles.rdbms.descriptor'));
    }
}
?>