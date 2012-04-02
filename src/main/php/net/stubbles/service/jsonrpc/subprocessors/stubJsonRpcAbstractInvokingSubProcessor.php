<?php
/**
 * JSON-RPC sub processor with basic invocation helper methods.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 * @version     $Id: stubJsonRpcAbstractInvokingSubProcessor.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcSubProcessor'
);
/**
 * JSON-RPC sub processor with basic invocation helper methods.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors
 */
abstract class stubJsonRpcAbstractInvokingSubProcessor extends stubBaseObject implements stubJsonRpcSubProcessor
{
    /**
     * Regexp to validate method param
     */
    const CLASS_AND_METHOD_PATTERN = '/^[a-zA-Z0-9_]+\.[a-zA-Z0-9_]+$/';

    /**
     * creates the method to call
     *
     * @param   array   $classMap
     * @param   string  $methodName
     * @param   string  $className   optional  if used with dojo's SMD
     * @return  array
     * @throws  stubException
     */
    protected function getClassAndMethod(array $classMap, $methodName, $className = null)
    {
        if (null === $className) {
            if (!preg_match(self::CLASS_AND_METHOD_PATTERN, $methodName)) {
                throw new stubException('Invalid request: method-Pattern has to be <className>.<methodName> '
                                       .'or service property has to be set.');
            }

            list($className, $methodName) = explode('.', $methodName);
        }

        if (isset($classMap[$className]) === false) {
            throw new stubException('Unknown class ' . $className . '.');
        }

        $clazz = new stubReflectionClass($classMap[$className]);
        if ($clazz->hasMethod($methodName) === false) {
            throw new stubException('Unknown method ' . $className . '.' . $methodName . '.');
        }

        $method = $clazz->getMethod($methodName);
        if ($method->hasAnnotation('WebMethod') === false) {
            throw new stubException('Method ' . $className . '.' . $methodName . ' is not a WebMethod.');
        }

        return array('class' => $clazz, 'method' => $method);
    }

    /**
     * invoke the requested method
     *
     * @param   stubInjector          $injector
     * @param   stubReflectionClass   $class
     * @param   stubReflectionMethod  $method
     * @param   array                 $params
     * @return  mixed
     * @throws  stubException
     */
    protected function invokeServiceMethod(stubInjector $injector, stubReflectionClass $class, stubReflectionMethod $method, array $params)
    {
        if ($method->getNumberOfRequiredParameters() > count($params)) {
            throw new stubException('Invalid amount of parameters passed.');
        }

        return $method->invokeArgs($injector->getInstance($class->getName()), $params);
    }
}
?>