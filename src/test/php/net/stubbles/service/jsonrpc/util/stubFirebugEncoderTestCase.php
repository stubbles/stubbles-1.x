<?php
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubFirebugEncoder.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @version     $Id: stubFirebugEncoderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::util::stubFirebugEncoder');
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubFirebugEncoder.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @group       service_jsonrpc
 */
class stubFirebugEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that the firebug encoder works as expected
     *
     * @test
     */
    public function encode()
    {
        $firebugEncoder = new stubFirebugEncoder();
        $this->assertEquals('error', $firebugEncoder->getLevel());
        $this->assertEquals("console.error('hello world');\n", $firebugEncoder->encode('hello world'));
        $firebugEncoder->setLevel('debug');
        $this->assertEquals('debug', $firebugEncoder->getLevel());
        $this->assertEquals("console.debug('hello');\nconsole.debug('world');\n", $firebugEncoder->encode("hello\nworld"));
    }

    /**
     * decoding firebug-encoded values is not possible
     *
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function decodeThrowsException()
    {
        $firebugEncoder = new stubFirebugEncoder();
        $this->assertFalse($firebugEncoder->isReversible());
        $firebugEncoder->decode('foo');
    }
}
?>