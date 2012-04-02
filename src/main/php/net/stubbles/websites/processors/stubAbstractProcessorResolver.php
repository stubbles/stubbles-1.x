<?php
/**
 * Basic abstract implementation of a processor resolver.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @version     $Id: stubAbstractProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::lang::exceptions::stubConfigurationException',
                      'net::stubbles::websites::processors::stubProcessorResolver'
);
/**
 * Basic abstract implementation of a processor resolver.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
abstract class stubAbstractProcessorResolver extends stubBaseObject implements stubProcessorResolver
{
    /**
     * injector
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * resolves the request and creates the appropriate processor
     *
     * @param   stubRequest    $request   the current request
     * @param   stubSession    $session   the current session
     * @param   stubResponse   $response  the current response
     * @return  stubProcessor
     * @throws  stubConfigurationException
     */
    public function resolve(stubRequest $request, stubSession $session, stubResponse $response)
    {
        $processorClassName = $this->doResolve($request, $session, $response);
        if (null == $processorClassName) {
            throw new stubConfigurationException('Configuration error: no processor specified.');
        }
        
        return $this->injector->getInstance($processorClassName);
    }

    /**
     * does the real resolving work
     *
     * @param   stubRequest   $request   the current request
     * @param   stubSession   $session   the current session
     * @param   stubResponse  $response  the current response
     * @return  string        full qualified classname of the processor to create
     */
    protected abstract function doResolve(stubRequest $request, stubSession $session, stubResponse $response);
}
?>