<?php
/**
 * JSON-RPC sub processor that handles dynamic proxy generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcGenerateProxiesSubProcessor.php 2626 2010-08-12 17:05:15Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractGenerateSubProcessor',
                      'net::stubbles::service::jsonrpc::util::stubJsonRpcProxyGenerator'
);
/**
 * JSON-RPC sub processor that handles dynamic proxy generation.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
class stubJsonRpcGenerateProxiesSubProcessor extends stubJsonRpcAbstractGenerateSubProcessor
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
        $classes   = $request->readParam('__generateProxy')->ifSatisfiesRegex('/^[A-Za-z,0-9_\.]+$/');
        if ('__all' !== $classes) {
            $classes = explode(',', $classes);
        }
            
        $response->write($namespace . " = {};\n\n");
        $generator = $this->getProxyGenerator();
        foreach ($config->getSection('classmap') as $jsClass => $fqClassName) {
            if (is_array($classes) === false || in_array($jsClass, $classes) === true) {
                try {
                    $response->write($generator->generateJavascriptProxy($fqClassName, $jsClass, $namespace));
                } catch (Exception $e) {
                    $this->handleException($injector, $e, $response, 'Generation of proxy for ' . $fqClassName . ' failed.');
                }
            }
        }
    }

    /**
     * helper method to create the proxy generator
     *
     * @return  stubJsonRpcProxyGenerator
     */
    // @codeCoverageIgnoreStart
    protected function getProxyGenerator()
    {
        return new stubJsonRpcProxyGenerator();
    }
    // @codeCoverageIgnoreEnd
}
?>