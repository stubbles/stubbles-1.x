<?php
/**
 * JSON-RPC sub processor that handles get requests.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcGetSubProcessor.php 2626 2010-08-12 17:05:15Z mikey $
 */
stubClassLoader::load('net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcAbstractInvokingSubProcessor',
                      'net::stubbles::service::jsonrpc::stubJsonRpcWriter'
);
/**
 * JSON-RPC sub processor that handles get requests.
 *
 * This is mainly used for debugging purposes.
 *
 * http://localhost/stubbles/docroot/json.php?
 * <paramName>=2
 * [&<paramName>=3]*
 * &method=<classname>.<methodname>
 * &id=186252
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
class stubJsonRpcGetSubProcessor extends stubJsonRpcAbstractInvokingSubProcessor
{
    /**
     * Regexp to validate param param
     */
    const PARAM_PATTERN = '/^[a-zA-Z0-9_]+$/';
    /**
     * Regexp to validate id param
     */
    const ID_PATTERN    = '/^\d{6,7}$/';

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
        $requestId = $request->readParam('id')->ifSatisfiesRegex(self::ID_PATTERN);
        if (null === $requestId) {
            $response->write(stubJsonRpcWriter::writeFault($requestId, 'Invalid request: No id given.'));
            return;
        }
        
        $method = $request->readParam('method')->unsecure();
        if (null === $method) {
            $response->write(stubJsonRpcWriter::writeFault($requestId, 'Invalid request: No method given.'));
            return;
        }
        
        try {
            $reflect   = $this->getClassAndMethod($config->getSection('classmap'), $method);
            $params    = $this->retrieveGETParams($request, $reflect['method']);
            $result    = $this->invokeServiceMethod($injector, $reflect['class'], $reflect['method'], $params);
            $response->write(stubJsonRpcWriter::writeResponse($requestId, $result));
        } catch (Exception $e) {
            $response->write(stubJsonRpcWriter::writeFault($requestId, $e->getMessage()));
        }
    }

    /**
     * Get the parameters from the GET request
     *
     * @param   stubRequest           $request
     * @param   stubReflectionMethod  $method
     * @return  array
     * @throws  stubException
     */
    protected function retrieveGETParams(stubRequest $request, stubReflectionMethod $method)
    {
        $paramValues  = array();
        foreach ($method->getParameters() as $param) {
            $paramName  = $param->getName();
            $paramValue = $request->readParam($paramName)->ifSatisfiesRegex(self::PARAM_PATTERN);
            if (null === $paramValue) {
                throw new stubException('Param '. $paramName . ' is missing.');
            }

            array_push($paramValues, $paramValue);
        }

        return $paramValues;
    }
}
?>