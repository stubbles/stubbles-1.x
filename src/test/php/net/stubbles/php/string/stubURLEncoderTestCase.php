<?php
/**
 * Tests for net::stubbles::php::string::stubURLEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubURLEncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubURLEncoder');
/**
 * Tests for net::stubbles::php::string::stubURLEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubURLEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubURLEncoder
     */
    protected $urlEncoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->urlEncoder = new stubURLEncoder();
    }

    /**
     * assure that the encoder works as expected
     *
     * @test
     */
    public function encode()
    {
        $this->assertEquals(urlencode('http://example.com/hello world'), $this->urlEncoder->encode('http://example.com/hello world'));
    }

    /**
     * assure that the decoder works as expected
     *
     * @test
     */
    public function decode()
    {
        $this->assertEquals('http://example.com/hello world', $this->urlEncoder->decode(urlencode('http://example.com/hello world')));
    }

    /**
     * url encoding is always reversible
     *
     * @test
     */
    public function alwaysReversible()
    {
        $this->assertTrue($this->urlEncoder->isReversible());
    }
}
?>