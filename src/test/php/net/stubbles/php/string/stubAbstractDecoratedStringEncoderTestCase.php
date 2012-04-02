<?php
/**
 * Tests for net::stubbles::php::string::stubAbstractDecoratedStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubAbstractDecoratedStringEncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractDecoratedStringEncoder');
/**
 * Concrete implementation test the stubAbstractDecoratedStringEncoder::__call() method.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class TeststubAbstractStringEncoderWithAdditionalMethod extends stubAbstractStringEncoder
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

    /**
     * an additional method to be used in the test
     *
     * @param   string  $argument
     * @return  string
     */
    public function additionalMethod($argument)
    {
        return 'called with argument ' . $argument;
    }
}
/**
 * Concrete implementation of the stubAbstractDecoratedStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 */
class TeststubAbstractDecoratedStringEncoder extends stubAbstractDecoratedStringEncoder
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
 * Tests for net::stubbles::php::string::stubAbstractDecoratedStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 */
class stubAbstractDecoratedStringEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubAbstractDecoratedStringEncoder
     */
    protected $abstractDecoratedEncoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->abstractDecoratedEncoder = new TeststubAbstractDecoratedStringEncoder(new TeststubAbstractStringEncoderWithAdditionalMethod());
    }

    /**
     * assert that a call to the additional method will be passed thru the
     * AbstractDecoratedStringEncoder
     *
     * @test
     */
    public function callAdditionalMethod()
    {
        $this->assertEquals('called with argument foo', $this->abstractDecoratedEncoder->additionalMethod('foo'));
    }

    /**
     * assert that a call to a non-existing method will throws a stubMethodNotSupportedException
     *
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function callAdditionalNonExistingMethod()
    {
        $this->abstractDecoratedEncoder->doesNotExist('foo');
    }
}
?>