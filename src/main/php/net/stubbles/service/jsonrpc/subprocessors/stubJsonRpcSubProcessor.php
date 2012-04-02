<?php
/**
 * Interface for JSON-RPC sub processors.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcSubProcessor.php 2222 2009-06-09 21:55:06Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::lang::stubProperties'
);
/**
 * Interface for JSON-RPC sub processors.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
interface stubJsonRpcSubProcessor extends stubObject
{
    /**
     * does the processing of the subtask
     *
     * @param  stubRequest     $request   current request
     * @param  stubSession     $session   current session
     * @param  stubResponse    $response  current response
     * @param  stubInjector    $injector  injector instance
     * @param  stubProperties  $config    json-rpc config
     */
    public function process(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, stubProperties $config);
}
?>