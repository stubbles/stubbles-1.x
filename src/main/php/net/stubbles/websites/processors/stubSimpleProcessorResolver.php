<?php
/**
 * A very simple implementation for the processor resolver which returns always
 * the same processor.
 *
 * @package     stubbles
 * @subpackage  websites_processors
 * @version     $Id: stubSimpleProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubAbstractProcessorResolver');
/**
 * A very simple implementation for the processor resolver which returns always
 * the same processor.
 *
 * @package     stubbles
 * @subpackage  websites_processors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubSimpleProcessorResolver extends stubAbstractProcessorResolver
{
    /**
     * full qualified classname of the processor to use
     * 
     * @var  string
     */
    protected $processorClassName   = null;
    /**
     * descriptor for interceptor config
     *
     * @var  string
     */
    protected $interceptorDescriptor = null;

    /**
     * constructor
     *
     * @param  stubInjector  $injector               injector
     * @param  string        $processorClassName     full qualified class name of the processor
     * @param  string        $interceptorDescriptor  optional  the interceptor descriptor
     */
    public function __construct(stubInjector $injector, $processorClassName, $interceptorDescriptor = null)
    {
        $this->injector              = $injector;
        $this->processorClassName    = $processorClassName;
        $this->interceptorDescriptor = ((null == $interceptorDescriptor) ? ('interceptors') : ($interceptorDescriptor));
    }

    /**
     * returns interceptor descriptor for given processor
     *
     * @param   stubRequest  $request  the current request
     * @return  string
     */
    public function getInterceptorDescriptor(stubRequest $request)
    {
        return $this->interceptorDescriptor;
    }

    /**
     * does the real resolving work
     *
     * @param   stubRequest   $request   the current request
     * @param   stubSession   $session   the current session
     * @param   stubResponse  $response  the current response
     * @return  string        full qualified classname of the processor to create
     */
    protected function doResolve(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return $this->processorClassName;
    }
}
?>