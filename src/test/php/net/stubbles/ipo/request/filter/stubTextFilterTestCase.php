<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubTextFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubTextFilterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubTextFilter');
/**
 * Tests for net::stubbles::ipo::request::filter::stubTextFilter.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubTextFilterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the instance to test
     *
     * @var  stubTextFilter
     */
    protected $textFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->textFilter = new stubTextFilter($this->mockRequestValueErrorFactory);
    }

    /**
     * assure that filtering null is ok when no input required and throws an
     * exception when input required
     *
     * @test
     */
    public function nullSucceeds()
    {
        $this->assertNull($this->textFilter->execute(null));
    }

    /**
     * assure that filtering an empty string is ok when no input required and
     * throws an exception when input required
     *
     * @test
     */
    public function emptyValue()
    {
        $this->assertEquals('', $this->textFilter->execute(''));
    }

    /**
     * assure that filtering a string with invalid character returns the string
     * without invalid characters and that string length is considered after
     * removing invalid characters
     *
     * @test
     */
    public function corrections()
    {
        $this->assertEquals("ab\ncde\n'kkk", $this->textFilter->execute("ab\ncde\r\n\'kkk<b>"));
    }

    /**
     * assure that filtering of HTML works correct
     *
     * @test
     */
    public function html()
    {
        $text = 'this is <b>bold</b> and <i>cursive</i> and <u>underlined</u>';
        $this->assertEquals('this is bold and cursive and underlined', $this->textFilter->execute($text));
        
        $this->textFilter->setAllowedTags(array('b', 'i'));
        $this->assertEquals(array('b', 'i'), $this->textFilter->getAllowedTags());
        $this->assertEquals('this is <b>bold</b> and <i>cursive</i> and underlined', $this->textFilter->execute($text));
        
        $this->textFilter->setAllowedTags(array('b', 'i', 'a'));
        $this->assertEquals(array('b', 'i', 'a'), $this->textFilter->getAllowedTags());
        $this->assertEquals('this is <b>bold</b> and <i>cursive</i> and underlined with a <a href="http://example.org/">link</a>', $this->textFilter->execute($text . ' with a <a href="http://example.org/">link</a>'));
    }
}
?>