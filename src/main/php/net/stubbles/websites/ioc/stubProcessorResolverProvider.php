<?php
/**
 * Provider for the processor resolver depending on runtime mode.
 *
 * @package     stubbles
 * @subpackage  websites_ioc
 * @version     $Id: stubProcessorResolverProvider.php 3162 2011-08-12 14:25:03Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::lang::stubMode'
);
/**
 * Provider for the processor resolver depending on runtime mode.
 *
 * @package     stubbles
 * @subpackage  websites_ioc
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubProcessorResolverProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * mode we are running in
     *
     * @var  stubMode
     */
    protected $mode;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubInjector  $injector  injector instance to create processor resolver depending on mode
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * sets the runtime mode we are running in
     *
     * @param  stubMode      $mode
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->mode = $mode;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        if (null === $this->mode || $this->mode->isCacheEnabled() === true) {
            return $this->injector->getInstance('net::stubbles::websites::cache::stubCachingProcessorResolver');
        }
        
        return $this->injector->getInstance('stubProcessorResolver', 'net.stubbles.websites.processor.defaultResolver');
    }
}
?>