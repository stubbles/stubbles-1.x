<?php
/**
 * Tests for net::stubbles::php::string::stubMd5Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubMd5EncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubMd5Encoder');
/**
 * Tests for net::stubbles::php::string::stubMd5Encoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubMd5EncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that the md5 encoder works as expected
     *
     * @test
     */
    public function withoutPrefixWithoutPostfix()
    {
        $md5Encoder = new stubMd5Encoder();
        $this->assertEquals(md5('hello world'), $md5Encoder->encode('hello world'));
    }

    /**
     * assure that the md5 encoder works as expected
     *
     * @test
     */
    public function withPrefixWithoutPostfix()
    {
        $md5Encoder = new stubMd5Encoder('foo ');
        $this->assertEquals(md5('foo hello world'), $md5Encoder->encode('hello world'));
        $md5Encoder->setPrefix('bar ');
        $this->assertEquals(md5('bar hello world'), $md5Encoder->encode('hello world'));
    }

    /**
     * assure that the md5 encoder works as expected
     *
     * @test
     */
    public function withoutPrefixWithPostfix()
    {
        $md5Encoder = new stubMd5Encoder('', ' foo');
        $this->assertEquals(md5('hello world foo'), $md5Encoder->encode('hello world'));
        $md5Encoder->setPostfix(' bar');
        $this->assertEquals(md5('hello world bar'), $md5Encoder->encode('hello world'));
    }

    /**
     * assure that the md5 encoder works as expected
     *
     * @test
     */
    public function withPrefixWithPostfix()
    {
        $md5Encoder = new stubMd5Encoder('foo ', ' bar');
        $this->assertEquals(md5('foo hello world bar'), $md5Encoder->encode('hello world'));
        $md5Encoder->setPrefix('baz ');
        $this->assertEquals(md5('baz hello world bar'), $md5Encoder->encode('hello world'));
        $md5Encoder->setPostfix(' foo');
        $this->assertEquals(md5('baz hello world foo'), $md5Encoder->encode('hello world'));
    }

    /**
     * decoding md5-encoded values is not possible
     *
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function decodeThrowsException()
    {
        $md5Encoder = new stubMd5Encoder();
        $this->assertFalse($md5Encoder->isReversible());
        $md5Encoder->decode('foo');
    }
}
?>