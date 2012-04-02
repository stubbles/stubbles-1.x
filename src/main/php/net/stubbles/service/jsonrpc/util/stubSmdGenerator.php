<?php
/**
 * Class to generate service method descriptions for JSON-RPC proxies.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util
 * @version     $Id: stubSmdGenerator.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass');
/**
 * Class to generate service method descriptions for JSON-RPC proxies.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util
 */
class stubSmdGenerator extends stubBaseObject
{
    /**
     * URL of the service
     *
     * @var  string
     */
    protected $serviceUrl;

    /**
     * create a new generator
     *
     * @param  string  $serviceUrl
     */
    public function __construct($serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
    }

    /**
     * generate JS proxy for a specified class
     *
     * @param   string  $className  name of the class to generate the proxy from
     * @param   string  $jsClass    optional  name of the generated javascript proxy
     * @return  string
     */
    public function generateSmd($className, $jsClass = null)
    {
        $smdData = new stdClass();
        $smdData->SMDVersion  = 1;
        $smdData->serviceType = 'JSON-RPC';
        $smdData->serviceURL  = $this->serviceUrl;
        $smdData->methods     = array();
        if (null !== $jsClass) {
            $smdData->objectName = $jsClass;
        }

        $clazz = new stubReflectionClass($className);
        foreach ($clazz->getMethods() as $method) {
            if ($method->hasAnnotation('WebMethod') === true) {
                $methodDef             = new stdClass();
                $methodDef->name       = $method->getName();
                $methodDef->parameters = array();
                $smdData->methods[]    = $methodDef;
                foreach ($method->getParameters() as $parameter) {
                    $paramDef = new stdClass();
                    $paramDef->name = $parameter->getName();
                    $methodDef->parameters[] = $paramDef;
                }
            }
        }
        
        return json_encode($smdData);
    }
}
?>