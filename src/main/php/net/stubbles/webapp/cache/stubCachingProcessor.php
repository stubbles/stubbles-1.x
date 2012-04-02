<?php
/**
 * Processor that can be applied onto any processor to cache its documents.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubCachingProcessor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::cache::stubWebsiteCache',
                      'net::stubbles::webapp::processor::stubAbstractProcessorDecorator'
);
/**
 * Processor that can be applied onto any processor to cache its documents.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 */
class stubCachingProcessor extends stubAbstractProcessorDecorator
{
    /**
     * the request
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * the created response
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * website cache to be used
     *
     * @var  stubWebsiteCache
     */
    protected $cache;

    /**
     * constructor
     *
     * @param  stubProcessor     $processor
     * @param  stubRequest       $request
     * @param  stubResponse      $response
     * @param  stubWebsiteCache  $websiteCache
     */
    public function __construct(stubProcessor $processor, stubRequest $request, stubResponse $response, stubWebsiteCache $websiteCache)
    {
        $this->processor = $processor;
        $this->request   = $request;
        $this->response  = $response;
        $this->cache     = $websiteCache;
    }

    /**
     * processes the request
     */
    public function process()
    {
        $isCachable = $this->processor->isCachable();
        if (true === $isCachable) {
            $this->cache->addCacheVars($this->processor->getCacheVars());
            $this->cache->addCacheVar('ssl', $this->processor->isSsl());
            if ($this->cache->retrieve($this->request, $this->response, $this->processor->getRouteName()) === true) {
                return;
            }
        }
        
        $this->processor->process();
        if (true === $isCachable) {
            $this->cache->store($this->request, $this->response, $this->processor->getRouteName());
        }
    }
}
?>