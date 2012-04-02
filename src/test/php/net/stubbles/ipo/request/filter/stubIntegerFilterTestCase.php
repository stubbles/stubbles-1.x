<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubIntegerFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubIntegerFilterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubIntegerFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubIntegerFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubIntegerFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned the expected way
     *
     * @test
     */
    public function value()
    {
        $integerFilter = new stubIntegerFilter();
        $this->assertEquals(8, $integerFilter->execute(8));
    }

    /**
     * assure that 0 is returned when value not set or empty when no value
     * is required
     *
     * @test
     */
    public function unsetOrOtherValues()
    {
        $integerFilter = new stubIntegerFilter();
        $this->assertNull($integerFilter->execute(null));
        $this->assertEquals(0, $integerFilter->execute(''));
        $this->assertEquals(1, $integerFilter->execute(true));
        $this->assertEquals(0, $integerFilter->execute(false));
    }

    /**
     * assure that a given double is returned as integer
     *
     * @test
     */
    public function float()
    {
        $integerFilter = new stubIntegerFilter();
        $this->assertEquals(1, $integerFilter->execute(1.564));
    }
}
?>