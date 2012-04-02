<?php
/**
 * Processor resolver to decorate another processor resolver and return a caching processor.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @version     $Id: stubCachingProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::cache::stubCachingProcessor',
                      'net::stubbles::websites::cache::stubWebsiteCache',
                      'net::stubbles::websites::processors::stubProcessorResolver'
);
/**
 * Processor resolver to decorate another processor resolver and return a caching processor.
 * 
 * @package     stubbles
 * @subpackage  websites_processors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubCachingProcessorResolver extends stubBaseObject implements stubProcessorResolver
{
    /**
     * decorated processor resolver
     *
     * @var  stubProcessorResolver
     */
    protected $processorResolver;
    /**
     * originally resolved processor
     *
     * @var  string
     */
    protected $originalProcessor;
    /**
     * website cache implementation to be used
     *
     * @var  stubWebsiteCache
     */
    protected $websiteCache;

    /**
     * constructor
     *
     * @param  stubProcessorResolver  $processorResolver
     * @param  stubWebsiteCache       $websiteCache
     * @Inject
     * @Named{processorResolver}('net.stubbles.websites.processor.defaultResolver')
     */
    public function __construct(stubProcessorResolver $processorResolver, stubWebsiteCache $websiteCache)
    {
        $this->processorResolver = $processorResolver;
        $this->websiteCache      = $websiteCache;
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
        return new stubCachingProcessor($this->processorResolver->resolve($request, $session, $response),
                                        $request,
                                        $response,
                                        $this->websiteCache
               );
    }
}
?>