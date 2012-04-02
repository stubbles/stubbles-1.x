<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubStringFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubStringFilterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStringFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubStringFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubStringFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  stubStringFilter
     */
    protected $stringFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->stringFilter = new stubStringFilter();
    }

    /**
     * assure that filtering null is ok when no input required and throws an
     * exception when input required
     *
     * @test
     */
    public function nullSucceeds()
    {
        $this->assertNull($this->stringFilter->execute(null));
    }

    /**
     * assure that filtering an empty string is ok when no input required and
     * throws an exception when input required
     *
     * @test
     */
    public function emptyValue()
    {
        $this->assertEquals('', $this->stringFilter->execute(''));
    }

    /**
     * assure that filtering a string with invalid character returns the string
     * without invalid characters
     *
     * @test
     */
    public function corrections()
    {
        $this->assertEquals("abcde'kkk", $this->stringFilter->execute("ab\ncde\r\n\'kkk<b>"));
    }
}
?>