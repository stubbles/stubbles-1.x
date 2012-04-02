<?php
/**
 * Test for net::stubbles::ioc::stubValueInjectionProvider.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubValueInjectionProviderTestCase.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubValueInjectionProvider');
/**
 * Test for net::stubbles::ioc::stubValueInjectionProvider.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubValueInjectionProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * should provide given value
     *
     * @test
     */
    public function shouldProvideGivenValue()
    {
        $valueInjectorProvider = new stubValueInjectionProvider('value');
        $this->assertEquals('value', $valueInjectorProvider->get());
    }
}
?>