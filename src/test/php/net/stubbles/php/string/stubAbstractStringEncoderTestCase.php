<?php
/**
 * Tests for net::stubbles::php::string::stubAbstractStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubAbstractStringEncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractStringEncoder');
/**
 * Concrete implementation to test the stubAbstractStringEncoder::apply() method.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class TeststubAbstractStringEncoder extends stubAbstractStringEncoder
{
    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        return 'encoded';
    }

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function decode($string)
    {
        return 'decoded';
    }
}
/**
 * Tests for net::stubbles::php::string::stubAbstractStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 */
class stubAbstractStringEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubAbstractStringEncoder
     */
    protected $abstractEncoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->abstractEncoder = new TeststubAbstractStringEncoder();
    }

    /**
     * assure that encode() will be applied
     *
     * @test
     */
    public function encodeMode()
    {
        $this->assertEquals('encoded', $this->abstractEncoder->apply('foo', stubStringEncoder::MODE_ENCODE));
    }

    /**
     * assure that decode() will be applied
     *
     * @test
     */
    public function decodeMode()
    {
        $this->assertEquals('decoded', $this->abstractEncoder->apply('foo', stubStringEncoder::MODE_DECODE));
    }

    /**
     * assert that an invalid mode triggers an illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidMode()
    {
        $this->abstractEncoder->apply('foo', 'invalid');
    }

    /**
     * string encoders are reversible by default
     *
     * @test
     */
    public function alwaysReversibleByDefault()
    {
        $this->assertTrue($this->abstractEncoder->isReversible());
    }
}
?>