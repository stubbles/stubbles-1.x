<?php
/**
 * JSON-RPC processor (generic proxy for web services).
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc
 * @version     $Id: stubJsonRpcProcessor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::lang::stubProperties',
                      'net::stubbles::webapp::processor::stubAbstractProcessor'
);
/**
 * JSON-RPC processor (generic proxy for web services).
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc
 * @link        http://json-rpc.org/wiki/specification
 */
class stubJsonRpcProcessor extends stubAbstractProcessor
{
    /**
     * injector
     *
     * @var  stubInjector $injector
     */
    protected $injector;
    /**
     * configuration with list of client classes
     *
     * @var  stubProperties
     */
    protected $config;

    /**
     * constructor
     *
     * @param   stubRequest   $request     current request
     * @param   stubSession   $session     current session
     * @param   stubResponse  $response    current response
     * @param   stubInjector  $injector    injector instance
     * @param   string        $configPath  path to config file
     * @throws  stubFileNotFoundException
     * @Inject
     * @Named{configPath}('net.stubbles.config.path')
     */
    public function __construct(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, $configPath)
    {
        $this->config   = stubProperties::fromFile($configPath . DIRECTORY_SEPARATOR . 'json-rpc-service.ini');
        $this->injector = $injector;
        parent::__construct($request, $session, $response);
    }

    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return null;
    }

    /**
     * processes the request
     *
     * This method only dispatches the request to different subprocessors.
     *
     * @return  stubProcessor
     */
    public function process()
    {
        $fqClassName = $this->getSubProcessorClassName();
        $nqClassName = stubClassLoader::getNonQualifiedClassName($fqClassName);
        if (class_exists($nqClassName, false) === false) {
            stubClassLoader::load($fqClassName);
        }

        $subProcessor = new $nqClassName();
        $this->response->addHeader('Content-Type', $this->config->parseString('config', 'content-type', 'application/json'));
        $subProcessor->process($this->request, $this->session, $this->response, $this->injector, $this->config);
        return $this;
    }

    /**
     * returns the subprocessor class to be used
     *
     * @return  string
     */
    protected function getSubProcessorClassName()
    {
        if ($this->request->getMethod() === 'post') {
            return 'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcPostSubProcessor';
        } elseif ($this->request->hasParam('__generateProxy') === true) {
            return 'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateProxiesSubProcessor';
        } elseif ($this->request->hasParam('__smd') === true) {
            return 'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGenerateSmdSubProcessor';
        }

        return 'net::stubbles::service::jsonrpc::subprocessors::stubJsonRpcGetSubProcessor';
    }
}
?>