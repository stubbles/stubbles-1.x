<?php
/**
 * Tests for net::stubbles::ipo::request::stubRequestValueErrorFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_test
 * @version     $Id: stubRequestValueErrorFactoryTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::stubRequestValueErrorFactory'
);
/**
 * Tests for net::stubbles::ipo::request::stubRequestValueErrorFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_test
 * @group       ipo
 * @group       ipo_request
 */
class stubRequestValueErrorFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function noBindingsRequiredToCreateRveFactoryInstance()
    {
        $injector   = new stubInjector();
        $rveFactory = $injector->getInstance('stubRequestValueErrorFactory');
        $this->assertInstanceOf('stubRequestValueErrorFactory', $rveFactory);
        $this->assertInstanceOf('stubRequestValueErrorPropertiesFactory', $rveFactory);
        $this->assertSame($rveFactory, $injector->getInstance('stubRequestValueErrorFactory'));
    }
}
?>