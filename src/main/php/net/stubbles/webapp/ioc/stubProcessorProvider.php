<?php
/**
 * Injection provider for processor instances.
 *
 * @package     stubbles
 * @subpackage  webapp_ioc
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::lang::stubMode',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::webapp::processor::stubProcessor'
);
/**
 * Injection provider for processor instances.
 *
 * The provider will create the processor based on the requested name. The
 * name must be defined in the processor map and pointing to a processor class
 * name.
 *
 * In case authentication is enabled the created processor will be decorated
 * with an auth processor.
 *
 * In case no runtime mode is set or the current runtime mode enables caching
 * the created processor will be decorated with a caching processor.
 *
 * In case both authentication and caching are enabled the created processor
 * will be decorated with an auth processor which in turn gets decorated by a
 * caching processor.
 *
 * @package     stubbles
 * @subpackage  webapp_ioc
 * @since       1.7.0
 */
class stubProcessorProvider extends stubBaseObject implements stubInjectionProvider
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
     * contains request data
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * session container
     *
     * @var  stubResponse
     */
    protected $response;
    /**
     * map of processor names to processor classes
     *
     * @var  array<string,string>
     */
    protected $processorMap;
    /**
     * switch whether authentication is enabled or not
     *
     * @var  bool
     */
    protected $authEnabled;

    /**
     * constructor
     *
     * @param  stubInjector          $injector      injector instance to create processor instance
     * @param  stubRequest           $request       request data container
     * @param  stubResponse          $response       session container
     * @param  array<string,string>  $processorMap  map of processor names to processor classes
     * @param  bool                  $authEnabled   switch whether authentication is enabled or not
     * @Inject
     * @Named{processorMap}('net.stubbles.webapp.processor.map')
     * @Named{authEnabled}('net.stubbles.webapp.auth')
     */
    public function __construct(stubInjector $injector,
                                stubRequest $request,
                                stubResponse $response,
                                $processorMap,
                                $authEnabled)
    {
        $this->injector     = $injector;
        $this->request      = $request;
        $this->response      = $response;
        $this->processorMap = $processorMap;
        $this->authEnabled  = $authEnabled;
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
     * Parameter $name is marked as optional because interface is defined this
     * way.
     *
     * @param   string  $name  optional
     * @return  mixed
     * @throws  stubIllegalArgumentException
     * @throws  stubRuntimeException
     */
    public function get($name = null)
    {
        if (null === $name) {
            throw new stubIllegalArgumentException('$name can not be null, need to identify processor.');
        }

        if (isset($this->processorMap[$name]) === false) {
            throw new stubRuntimeException('Processor named ' . $name . ' can not be created, not defined in processor map (net.stubbles.webapp.processor.map).');
        }


        return $this->decorateWithCachingProcessor($this->decorateWithAuthProcessor($this->injector->getInstance($this->processorMap[$name])));
    }

    /**
     * decorates given processor with auth processor if auth is enabled
     *
     * @param   stubProcessor  $processor
     * @return  stubProcessor
     */
    protected function decorateWithAuthProcessor(stubProcessor $processor)
    {
        if (false === $this->authEnabled) {
            return $processor;
        }

        stubClassLoader::load('net::stubbles::webapp::auth::stubAuthProcessor');
        return new stubAuthProcessor($processor,
                                     $this->request,
                                     $this->response,
                                     $this->injector->getInstance('stubAuthHandler')
               );
    }

    /**
     * decorates given processor with caching processor if caching is enabled
     *
     * Caching is enabled if there is no specific runtime mode set or if the
     * current runtime mode allows caching.
     *
     * @param  stubProcessor  $processor
     * @return stubProcessor
     */
    protected function decorateWithCachingProcessor(stubProcessor $processor)
    {
        if (null !== $this->mode && $this->mode->isCacheEnabled() === false) {
            return $processor;
        }

        stubClassLoader::load('net::stubbles::webapp::cache::stubCachingProcessor');
        return new stubCachingProcessor($processor,
                                        $this->request,
                                        $this->response,
                                        $this->injector->getInstance('stubWebsiteCache')
               );
    }
}
?>