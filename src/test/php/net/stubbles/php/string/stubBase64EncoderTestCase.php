<?php
/**
 * Tests for net::stubbles::php::string::stubBase64Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubBase64EncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubBase64Encoder');
/**
 * Tests for net::stubbles::php::string::stubBase64Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubBase64EncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubBase64Encoder
     */
    protected $base64Encoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->base64Encoder = new stubBase64Encoder();
    }

    /**
     * assure that the encoder works as expected
     *
     * @test
     */
    public function encode()
    {
        $this->assertEquals(base64_encode('hello world'), $this->base64Encoder->encode('hello world'));
    }

    /**
     * assure that the decoder works as expected
     *
     * @test
     */
    public function decode()
    {
        $this->assertEquals('hello world', $this->base64Encoder->decode(base64_encode('hello world')));
    }

    /**
     * base64 is always reversible
     *
     * @test
     */
    public function alwaysReversible()
    {
        $this->assertTrue($this->base64Encoder->isReversible());
    }
}
?>