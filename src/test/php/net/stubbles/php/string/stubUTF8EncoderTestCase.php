<?php
/**
 * Tests for net::stubbles::php::string::stubUTF8Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubUTF8EncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubUTF8Encoder');
/**
 * Tests for net::stubbles::php::string::stubUTF8Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubUTF8EncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubUTF8Encoder
     */
    protected $utf8Encoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->utf8Encoder = new stubUTF8Encoder();
    }

    /**
     * assure that the encoder works as expected
     *
     * @test
     */
    public function encode()
    {
        $this->assertEquals('hällö wörld', $this->utf8Encoder->encode(utf8_decode('hällö wörld')));
        $this->assertEquals(313, $this->utf8Encoder->encode(313));
        $this->assertEquals(true, $this->utf8Encoder->encode(true));
    }

    /**
     * assure that the decoder works as expected
     *
     * @test
     */
    public function decode()
    {
        $this->assertEquals(utf8_decode('hällö wörld'), $this->utf8Encoder->decode('hällö wörld'));
        $this->assertEquals(313, $this->utf8Encoder->decode(313));
        $this->assertEquals(true, $this->utf8Encoder->decode(true));
    }

    /**
     * utf8 encoding is always reversible
     *
     * @test
     */
    public function alwaysReversible()
    {
        $this->assertTrue($this->utf8Encoder->isReversible());
    }
}
?>