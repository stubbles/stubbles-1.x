<?php
/**
 * Test for net::stubbles::service::jsonrpc::stubJsonRpcWriter.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @version     $Id: stubJsonRpcWriterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::stubJsonRpcWriter');
/**
 * Tests for net::stubbles::service::jsonrpc::stubJsonRpcWriter.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_test
 * @group       service_jsonrpc
 */
class stubJsonRpcWriterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test sending a fault
     *
     * @test
     */
    public function writeFault()
    {
        $this->assertEquals('{"id":"12345","result":null,"error":"Test error message."}', stubJsonRpcWriter::writeFault('12345', 'Test error message.'));
    }

    /**
     * test sending a response
     *
     * @test
     */
    public function writeResponseWithString()
    {
        $this->assertEquals('{"id":"12345","result":"string","error":null}', stubJsonRpcWriter::writeResponse('12345', 'string'));
    }

    /**
     * test sending a response
     *
     * @test
     */
    public function writeResponseWithBool()
    {
        $this->assertEquals('{"id":"12345","result":true,"error":null}', stubJsonRpcWriter::writeResponse('12345', true));
    }

    /**
     * test sending a response
     *
     * @test
     */
    public function writeResponseWithArray()
    {
        $this->assertEquals('{"id":"12345","result":[1,2,3],"error":null}', stubJsonRpcWriter::writeResponse('12345', array(1,2,3)));
    }
}
?>