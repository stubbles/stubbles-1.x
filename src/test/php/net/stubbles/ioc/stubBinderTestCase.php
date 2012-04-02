<?php
/**
 * Test for net::stubbles::ioc::stubBinder
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubBinderTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');

/**
 * Test for net::stubbles::ioc::stubBinder
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubBinderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Test the getInjector() method
     *
     * @test
     */
    public function getInjector()
    {
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $this->assertInstanceOf('stubInjector', $injector);
    }

    /**
     * Test, that the method will always return the same injector
     *
     * @test
     */
    public function sameInjector()
    {
        $binder    = new stubBinder();
        $injector  = $binder->getInjector();
        $injector2 = $binder->getInjector();
        $this->identicalTo($injector, $injector2);
    }
}
?>