<?php
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubJsonRpcProxyGenerator.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @version     $Id: stubJsonRpcProxyGeneratorTestCase.php 3225 2011-11-23 16:09:41Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::util::stubJsonRpcProxyGenerator');
/**
 * Tests for net::stubbles::service::jsonrpc::util::stubJsonRpcProxyGenerator.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util_test
 * @group       service_jsonrpc
 */
class stubJsonRpcProxyGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function generation()
    {
        $generator = new stubJsonRpcProxyGenerator();
        $code      = $generator->generateJavascriptProxy('org::stubbles::test::service::MathService', 'JsMathService', 'my.js.ns');
        $expected  = "my.js.ns.JsMathService = function(clientObj) {
    this.dispatcher = new stubbles.json.rpc.Client(clientObj);
};
my.js.ns.JsMathService.prototype.add = function() {
    return this.dispatcher.doCall('JsMathService.add', arguments);
};
my.js.ns.JsMathService.prototype.throwException = function() {
    return this.dispatcher.doCall('JsMathService.throwException', arguments);
};
";      
        $this->assertEquals($expected, $code);
    }

    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function generationWithoutJsClassAndNamespace()
    {
        $generator = new stubJsonRpcProxyGenerator();
        $code      = $generator->generateJavascriptProxy('org::stubbles::test::service::MathService');
        $expected  = "stubbles.json.proxy.MathService = function(clientObj) {
    this.dispatcher = new stubbles.json.rpc.Client(clientObj);
};
stubbles.json.proxy.MathService.prototype.add = function() {
    return this.dispatcher.doCall('MathService.add', arguments);
};
stubbles.json.proxy.MathService.prototype.throwException = function() {
    return this.dispatcher.doCall('MathService.throwException', arguments);
};
";      
        $this->assertEquals($expected, $code);
    }
}
?>