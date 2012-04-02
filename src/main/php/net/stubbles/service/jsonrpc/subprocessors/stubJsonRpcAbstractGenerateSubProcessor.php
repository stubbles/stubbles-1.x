<?php
/**
 * Basic JSON-RPC sub processor with helper methods for dynamic proxy generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcAbstractGenerateSubProcessor.php 2632 2010-08-13 18:31:42Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcSubProcessor'
);
/**
 * Basic JSON-RPC sub processor with helper methods for dynamic proxy generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
abstract class stubJsonRpcAbstractGenerateSubProcessor extends stubBaseObject implements stubJsonRpcSubProcessor
{
    /**
     * default javascript namespace
     *
     * @var  string
     */
    protected $jsNamespace = 'stubbles.json.proxy';

    /**
     * helper method to detect the service url
     *
     * @param   stubRequest  $request
     * @return  string
     */
    protected function getServiceUrl(stubRequest $request)
    {
        $tmp        = parse_url($request->getURI());
        $serviceUrl = '//' . $tmp['path'];
        if ($request->hasParam('processor') === true) {
            $serviceUrl .= '?processor=' . $request->readParam('processor')->unsecure();
        }
        
        return $serviceUrl;
    }

    /**
     * helper method to handle an exception
     *
     * @param  stubInjector  $injector
     * @param  Exception     $exception
     * @param  stubResponse  $response
     * @param  string        $introduction
     */
    protected function handleException(stubInjector $injector, Exception $exception, stubResponse $response, $introduction)
    {
        if ($injector->hasExplicitBinding('stubMode') === false || $injector->getInstance('stubMode')->name() === 'PROD') {
            return;
        }
        
        stubClassLoader::load('net::stubbles::service::jsonrpc::util::stubFirebugEncoder');
        $firebugEncoder = new stubFirebugEncoder();
        $response->write($firebugEncoder->encode($introduction));
        $response->write($firebugEncoder->encode($exception->__toString()));
    }
}
?>