<?php
/**
 * JSON-RPC sub processor that handles dynamic smd generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcGenerateSmdSubProcessor.php 2626 2010-08-12 17:05:15Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractGenerateSubProcessor',
                      'net::stubbles::service::jsonrpc::util::stubSmdGenerator'
);
/**
 * JSON-RPC sub processor that handles dynamic smd generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
class stubJsonRpcGenerateSmdSubProcessor extends stubJsonRpcAbstractGenerateSubProcessor
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
    public function process(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, stubProperties $config)
    {
        $namespace = $config->getValue('config', 'namespace', $this->jsNamespace);
        $class     = $request->readParam('__smd')->ifSatisfiesRegex('/^[A-Za-z0-9_\.]+$/');
        $generator = $this->getSmdGenerator($this->getServiceUrl($request) . '&__class=' . $class);
        // get rid of namespace for class matching
        $class     = preg_replace('/' . preg_quote($namespace) . '\./', '', $class);
        try {
            $response->write($generator->generateSmd($config->getValue('classmap', $class), $class));
        } catch (Exception $e) {
            $this->handleException($injector, $e, $response, 'Generation of SMD for ' . $config->getValue('classmap', $class) . ' failed.');
        }
    }

    /**
     * creates the smd generator
     *
     * @param   string            $serviceUrl
     * @return  stubSmdGenerator
     */
    // @codeCoverageIgnoreStart
    protected function getSmdGenerator($serviceUrl)
    {
        return new stubSmdGenerator($serviceUrl);
    }
    // @codeCoverageIgnoreEnd
}
?>