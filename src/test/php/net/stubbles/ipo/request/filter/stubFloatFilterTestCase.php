<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubFloatFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubFloatFilterTestCase.php 2320 2009-09-14 08:34:11Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFloatFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubFloatFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubFloatFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $floatFilter = new stubFloatFilter();
        $this->assertSame($floatFilter, $floatFilter->setDecimals(3));
        $this->assertEquals(8453, $floatFilter->execute('8.4533'));
        $this->assertEquals(8453, $floatFilter->execute('8.4538'));
        $this->assertEquals(8450, $floatFilter->execute('8.45'));
        $this->assertEquals(8000, $floatFilter->execute('8'));
        $this->assertEquals(8453, $floatFilter->execute(8.4533));
        $this->assertEquals(8453, $floatFilter->execute(8.4538));
        $this->assertEquals(8450, $floatFilter->execute(8.45));
        $this->assertEquals(8000, $floatFilter->execute(8));
    }

    /**
     * assure that 0 is returned when value not set or empty when no value
     * is required
     *
     * @test
     */
    public function unsetOrOtherValues()
    {
        $floatFilter = new stubFloatFilter();
        $this->assertSame($floatFilter, $floatFilter->setDecimals(3));
        $this->assertNull($floatFilter->execute(null));
        $this->assertEquals(0, $floatFilter->execute(''));
        $this->assertEquals(1000, $floatFilter->execute(true));
        $this->assertEquals(0, $floatFilter->execute(false));
    }

    /**
     * assure that the correct value depending on $decimal is returned
     *
     * @test
     */
    public function float()
    {
        $floatFilter = new stubFloatFilter();
        $this->assertSame($floatFilter, $floatFilter->setDecimals(2));
        $this->assertEquals(156, $floatFilter->execute('1.564'));
    }

    /**
     * assure that the correct value depending on $decimal is returned
     *
     * @test
     */
    public function decimalsNotSet()
    {
        $floatFilter = new stubFloatFilter();
        $this->assertEquals(1.564, $floatFilter->execute('1.564'));
    }
}
?>