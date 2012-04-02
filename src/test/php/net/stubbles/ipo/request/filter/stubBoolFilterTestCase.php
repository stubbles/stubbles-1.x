<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubBoolFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubBoolFilterTestCase.php 2506 2010-03-01 14:28:18Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubBoolFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubBoolFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @since       1.2.0
 */
class stubBoolFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubBoolFilter
     */
    protected $boolFilter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->boolFilter = new stubBoolFilter();
    }

    /**
     * @test
     */
    public function filtering1AsIntReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->execute(1));
    }

    /**
     * @test
     */
    public function filtering1AsStringReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->execute('1'));
    }

    /**
     * @test
     */
    public function filteringTrueAsStringReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->execute('true'));
    }

    /**
     * @test
     */
    public function filteringTrueAsBoolReturnsTrue()
    {
        $this->assertTrue($this->boolFilter->execute(true));
    }

    /**
     * @test
     */
    public function filtering0AsIntReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->execute(0));
    }

    /**
     * @test
     */
    public function filtering0AsStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->execute('0'));
    }

    /**
     * @test
     */
    public function filteringFalseAsStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->execute('false'));
    }

    /**
     * @test
     */
    public function filteringFalseAsBoolReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->execute(false));
    }

    /**
     * @test
     */
    public function filteringAnyStringReturnsFalse()
    {
        $this->assertFalse($this->boolFilter->execute('a string'));
    }
}
?>