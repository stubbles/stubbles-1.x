<?php
/**
 * Tests for net::stubbles::php::string::stubHTMLSpecialCharsEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubHTMLSpecialCharsEncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubHTMLSpecialCharsEncoder');
/**
 * Tests for net::stubbles::php::string::stubHTMLSpecialCharsEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubHTMLSpecialCharsEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubHTMLSpecialCharsEncoder
     */
    protected $htmlSpecialCharsEncoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->htmlSpecialCharsEncoder = new stubHTMLSpecialCharsEncoder();
    }

    /**
     * illegal quote style throws exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalQuoteStyleThrowsException()
    {
        $this->htmlSpecialCharsEncoder->setQuoteStyle(313);
    }

    /**
     * assure that the encoder works as expected
     *
     * @test
     */
    public function encode()
    {
        $this->assertEquals('&lt;&quot;hello&quot;&amp;&#039;world&#039;&gt;', $this->htmlSpecialCharsEncoder->encode('<"hello"&\'world\'>'));
        $this->htmlSpecialCharsEncoder->setQuoteStyle(ENT_NOQUOTES);
        $this->assertEquals('&lt;"hello"&amp;\'world\'&gt;', $this->htmlSpecialCharsEncoder->encode('<"hello"&\'world\'>'));
        $this->htmlSpecialCharsEncoder->setQuoteStyle(ENT_COMPAT);
        $this->assertEquals('&lt;&quot;hello&quot;&amp;\'world\'&gt;', $this->htmlSpecialCharsEncoder->encode('<"hello"&\'world\'>'));
        $this->htmlSpecialCharsEncoder->setQuoteStyle(ENT_QUOTES);
        $this->assertEquals('&lt;&quot;hello&quot;&amp;&#039;world&#039;&gt;', $this->htmlSpecialCharsEncoder->encode('<"hello"&amp;\'world\'>'));
        $this->htmlSpecialCharsEncoder->setDoubleEncode(true);
        $this->htmlSpecialCharsEncoder->setCharset('ISO-8859-1');
        $this->assertEquals('&lt;&quot;hell�&quot;&amp;amp;&#039;world&#039;&gt;', $this->htmlSpecialCharsEncoder->encode('<"hell�"&amp;\'world\'>'));
    }

    /**
     * assure that the decoder works as expected
     *
     * @test
     */
    public function decode()
    {
        $this->assertEquals('<"hello"&\'world\'>', $this->htmlSpecialCharsEncoder->decode('&lt;&quot;hello&quot;&amp;&#039;world&#039;&gt;'));
        $this->htmlSpecialCharsEncoder->setQuoteStyle(ENT_NOQUOTES);
        $this->assertEquals('<&quot;hello&quot;&&#039;world&#039;>', $this->htmlSpecialCharsEncoder->decode('&lt;&quot;hello&quot;&amp;&#039;world&#039;&gt;'));
        $this->htmlSpecialCharsEncoder->setQuoteStyle(ENT_COMPAT);
        $this->assertEquals('<"hello"&&#039;world&#039;>', $this->htmlSpecialCharsEncoder->decode('&lt;&quot;hello&quot;&amp;&#039;world&#039;&gt;'));
    }

    /**
     * html escaping is always reversible
     *
     * @test
     */
    public function alwaysReversible()
    {
        $this->assertTrue($this->htmlSpecialCharsEncoder->isReversible());
    }
}
?>