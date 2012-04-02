<?php
/**
 * Provider to create website cache instances.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @version     $Id: stubWebsiteCacheProvider.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::response::stubResponseSerializer',
                      'net::stubbles::util::log::stubLogger',
                      'net::stubbles::webapp::cache::stubCompositeWebsiteCache',
                      'net::stubbles::webapp::cache::stubDefaultWebsiteCache',
                      'net::stubbles::webapp::cache::stubGzipWebsiteCache',
                      'net::stubbles::webapp::cache::stubLoggingWebsiteCache'
);
/**
 * Provider to create website cache instances.
 *
 * @package     stubbles
 * @subpackage  webapp_cache
 * @since       1.7.0
 */
class stubWebsiteCacheProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * real cache
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * logger instance
     *
     * @var  stubLogger
     */
    protected $logger;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * sets logger instance in case cache hits and misses should be logged
     *
     * @param   stubLogger                $logger
     * @return  stubWebsiteCacheProvider
     * @Inject(optional=true)
     * @Named(stubLogger::LEVEL_INFO)
     */
    public function setLogger(stubLogger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * returns the website cache to be used
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        $cache              = $this->injector->getInstance('stubCacheContainer', 'websites');
        $responseSerializer = new stubResponseSerializer();
        $composite          = new stubCompositeWebsiteCache();
        $composite->addWebsiteCache(new stubGzipWebsiteCache($cache, $responseSerializer));
        $composite->addWebsiteCache(new stubDefaultWebsiteCache($cache, $responseSerializer));
        if (null !== $this->logger) {
            return new stubLoggingWebsiteCache($composite, $this->logger);
        }

        return $composite;
    }
}
?>