<?php
/**
 * Test for net::stubbles::ioc::stubInjector with the ProvidedBy annotation.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorProvidedByTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');
/**
 * Provider class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorProvidedByProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new stubInjectorProvidedyTestCase_Schst();
    }
}
/**
 * Helper class for the test
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @ProvidedBy('stubInjectorProvidedByProvider')
 */
interface stubInjectorProvidedByTestCase_Person
{
    public function sayHello();
}
/**
 * Helper class for the test
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @ProvidedBy(stubInjectorProvidedByProvider.class)
 */
interface stubInjectorProvidedByTestCase_Person2
{
    public function sayHello2();
}
/**
 * Helper class for the test
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorProvidedyTestCase_Schst implements stubInjectorProvidedByTestCase_Person, stubInjectorProvidedByTestCase_Person2
{
    public function sayHello()
    {
        return 'My name is schst.';
    }
    
    public function sayHello2()
    {
        return 'My name is still schst.';
    }
}
/**
 * Helper class for the test
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorProvidedByTestCase_Mikey implements stubInjectorProvidedByTestCase_Person
{
    public function sayHello()
    {
        return 'My name is mikey.';
    }
}
/**
 * Test for net::stubbles::ioc::stubInjector with the ProvidedBy annotation.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorProvidedByTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function defaultProvider()
    {
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $person   = $injector->getInstance('stubInjectorProvidedByTestCase_Person');
        $this->assertInstanceOf('stubInjectorProvidedyTestCase_Schst', $person);
    }

    /**
     * @test
     * @group  bug226
     */
    public function defaultProviderWithClass()
    {
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $person   = $injector->getInstance('stubInjectorProvidedByTestCase_Person2');
        $this->assertInstanceOf('stubInjectorProvidedyTestCase_Schst', $person);
    }

    /**
     * @test
     */
    public function override()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorProvidedByTestCase_Person')->to('stubInjectorProvidedByTestCase_Mikey');
        $injector = $binder->getInjector();
        $person   = $injector->getInstance('stubInjectorProvidedByTestCase_Person');
        $this->assertInstanceOf('stubInjectorProvidedByTestCase_Mikey', $person);
    }
}
?>