<?php
/**
 * Processor resolver to decorate another processor resolver and return an authentication based processor.
 * 
 * @package     stubbles
 * @subpackage  websites_processors_auth
 * @version     $Id: stubAuthProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubProcessorResolver',
                      'net::stubbles::websites::processors::auth::stubAuthHandler',
                      'net::stubbles::websites::processors::auth::stubAuthProcessor'
);
/**
 * Processor resolver to decorate another processor resolver and return an authentication based processor.
 * 
 * @package     stubbles
 * @subpackage  websites_processors_auth
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubAuthProcessorResolver extends stubBaseObject implements stubProcessorResolver
{
    /**
     * decorated processor resolver
     *
     * @var  stubProcessorResolver
     */
    protected $processorResolver;
    /**
     * injector to create authentication handler
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubProcessorResolver  $processorResolver
     * @param  stubInjector           $injector
     * @Inject
     * @Named{processorResolver}('net.stubbles.websites.processor.finalResolver')
     */
    public function __construct(stubProcessorResolver $processorResolver, stubInjector $injector)
    {
        $this->processorResolver = $processorResolver;
        $this->injector          = $injector;
    }

    /**
     * returns interceptor descriptor
     *
     * @param   stubRequest  $request
     * @return  string
     */
    public function getInterceptorDescriptor(stubRequest $request)
    {
        return $this->processorResolver->getInterceptorDescriptor($request);
    }

    /**
     * resolves the request and creates the appropriate processor
     *
     * @param   stubRequest    $request   the current request
     * @param   stubSession    $session   the current session
     * @param   stubResponse   $response  the current response
     * @return  stubProcessor
     */
    public function resolve(stubRequest $request, stubSession $session, stubResponse $response)
    {
        return new stubAuthProcessor($this->processorResolver->resolve($request, $session, $response),
                                     $request,
                                     $response,
                                     $this->injector->getInstance('stubAuthHandler')
               );
    }
}
?>