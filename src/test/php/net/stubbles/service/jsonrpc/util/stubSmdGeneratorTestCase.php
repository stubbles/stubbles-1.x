<?php
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubSmdGenerator.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @version     $Id: stubSmdGeneratorTestCase.php 3225 2011-11-23 16:09:41Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::util::stubSmdGenerator');
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubSmdGenerator.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @group       service_jsonrpc
 */
class stubSmdGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function generation()
    {
        $generator = new stubSmdGenerator('//example.com/foo.php');
        $code = $generator->generateSmd('org::stubbles::test::service::MathService', 'JsMathService');
        $expected = '{"SMDVersion":1,"serviceType":"JSON-RPC","serviceURL":"\/\/example.com\/foo.php","methods":[{"name":"add","parameters":[{"name":"a"},{"name":"b"}]},{"name":"throwException","parameters":[]}],"objectName":"JsMathService"}';
        $this->assertEquals($expected, $code);
    }

    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function generationWithoutJsClass()
    {
        $generator = new stubSmdGenerator('//example.com/foo.php');
        $code = $generator->generateSmd('org::stubbles::test::service::MathService');
        $expected = '{"SMDVersion":1,"serviceType":"JSON-RPC","serviceURL":"\/\/example.com\/foo.php","methods":[{"name":"add","parameters":[{"name":"a"},{"name":"b"}]},{"name":"throwException","parameters":[]}]}';
        $this->assertEquals($expected, $code);
    }
}
?>