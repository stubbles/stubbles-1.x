<?php
/**
 * Binding module for classes required by the front controller.
 *
 * @package     stubbles
 * @subpackage  websites_ioc
 * @version     $Id: stubWebsiteBindingModule.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Binding module for classes required by the front controller.
 *
 * @package     stubbles
 * @subpackage  websites_ioc
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubWebsiteBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * the default processor to use
     * 
     * @var  string
     */
    protected $defaultProcessorParamValue;
    /**
     * list of processors
     * 
     * @var  array<string,string>
     */
    protected $processors                 = array();
    /**
     * list of interceptor descriptors
     *
     * @var  array<string,string>
     */
    protected $interceptorDescriptors     = array();
    /**
     * class to be used as interceptor initializer
     *
     * @var  string
     */
    protected $interceptorInitializer     = 'net::stubbles::ipo::interceptors::stubPropertyBasedInterceptorInitializer';
    /**
     * list of router classes
     *
     * @var  array<string,string>
     */
    protected $routerClasses              = array();
    /**
     * list of default xml generators
     *
     * @var  array<string>
     */
    protected $xmlGenerators              = array('net::stubbles::websites::xml::generator::stubSessionXMLGenerator',
                                                  'net::stubbles::websites::xml::generator::stubRouteXMLGenerator',
                                                  'net::stubbles::websites::xml::generator::stubRequestXMLGenerator',
                                                  'net::stubbles::websites::xml::generator::stubModeXMLGenerator',
                                                  'net::stubbles::websites::xml::generator::stubVariantListGenerator'
                                            );
    /**
     * switch whether auth processor is enabled or not
     *
     * @var  bool
     */
    protected $authEnabled                = false;

    /**
     * constructor
     *
     * @param  string  $paramValue             value of the request parameter that identifies this processor
     * @param  string  $defaultProcessorClass  full qualified class name of the processor
     * @param  string  $interceptorDescriptor  optional  the interceptor descriptor
     * @param  string  $routerClass            optional  router class for the processor
     */
    public function __construct($paramValue, $defaultProcessorClass, $interceptorDescriptor = null, $routerClass = null)
    {
        $this->defaultProcessorParamValue = $paramValue;
        $this->addProcessor($paramValue, $defaultProcessorClass, $interceptorDescriptor, $routerClass);
    }

    /**
     * static constructor to allow method chaining
     *
     * @param   string                    $paramValue             value of the request parameter that identifies this processor
     * @param   string                    $defaultProcessorClass  full qualified class name of the processor
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @param   string                    $routerClass            optional  router class for the processor
     * @return  stubWebsiteBindingModule
     */
    public static function create($paramValue, $defaultProcessorClass, $interceptorDescriptor = null, $routerClass = null)
    {
        return new self($paramValue, $defaultProcessorClass, $interceptorDescriptor, $routerClass);
    }

    /**
     * static constructor which adds the xml processor as default processor
     *
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @param   string                    $routerClass            optional  router class for the processor
     * @return  stubWebsiteBindingModule
     */
    public static function createWithXmlProcessorAsDefault($interceptorDescriptor = null, $routerClass = 'net::stubbles::websites::processors::routing::stubPropertyBasedRouter')
    {
        return new self('xml', 'net::stubbles::websites::xml::stubXMLProcessor', $interceptorDescriptor, $routerClass);
    }

    /**
     * static constructor which adds the rest processor as default processor
     *
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @return  stubWebsiteBindingModule
     */
    public static function createWithRestProcessorAsDefault($interceptorDescriptor = null)
    {
        return new self('rest', 'net::stubbles::service::rest::stubRestProcessor', $interceptorDescriptor);
    }

    /**
     * adds a processor to the list of available processors
     * 
     * @param   string                    $paramValue             value of the request parameter that identifies this processor
     * @param   string                    $fqClassName            full qualified class name of the processor
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @param   string                    $routerClass            optional  router class for the processor
     * @return  stubWebsiteBindingModule
     */
    public function addProcessor($paramValue, $fqClassName, $interceptorDescriptor = null, $routerClass = null)
    {
        $this->processors[$paramValue]             = $fqClassName;
        $this->interceptorDescriptors[$paramValue] = $interceptorDescriptor;
        $this->routerClasses[$paramValue]          = $routerClass;
        return $this;
    }

    /**
     * enables the json-rpc processor
     *
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @return  stubWebsiteBindingModule
     */
    public function enableJsonRpc($interceptorDescriptor = 'interceptors-jsonrpc')
    {
        $this->addProcessor('jsonrpc', 'net::stubbles::service::jsonrpc::stubJsonRpcProcessor', $interceptorDescriptor);
        return $this;
    }

    /**
     * enables the rss processor
     *
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @return  stubWebsiteBindingModule
     */
    public function enableRss($interceptorDescriptor = 'interceptors-rss')
    {
        $this->addProcessor('rss', 'net::stubbles::xml::rss::stubRSSProcessor', $interceptorDescriptor);
        return $this;
    }

    /**
     * enables the rest processor
     *
     * @param   string                    $interceptorDescriptor  optional  the interceptor descriptor
     * @return  stubWebsiteBindingModule
     * @since   1.1.0
     */
    public function enableRest($interceptorDescriptor = 'interceptors-rest')
    {
        $this->addProcessor('rest', 'net::stubbles::service::rest::stubRestProcessor', $interceptorDescriptor);
        return $this;
    }

    /**
     * enable auth processor
     *
     * @return  stubWebsiteBindingModule
     */
    public function enableAuth()
    {
        $this->authEnabled = true;
        return $this;
    }

    /**
     * sets list of xml generators for xml processor
     *
     * @param   array<string>             $xmlGenerators
     * @return  stubWebsiteBindingModule
     */
    public function setXmlGenerators(array $xmlGenerators)
    {
        $this->xmlGenerators = $xmlGenerators;
        return $this;
    }

    /**
     * add a xml generator for xml processor
     *
     * @param   string                    $xmlGenerator
     * @return  stubWebsiteBindingModule
     */
    public function addXmlGenerator($xmlGenerator)
    {
        $this->xmlGenerators[] = $xmlGenerator;
        return $this;
    }

    /**
     * full qualified class name of interceptor initializer class to be used
     *
     * @param   string                    $interceptorInitializerClassName
     * @return  stubWebsiteBindingModule
     */
    public function usingInterceptorInitializer($interceptorInitializerClassName)
    {
        $this->interceptorInitializer = $interceptorInitializerClassName;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('stubInterceptorInitializer')
               ->to($this->interceptorInitializer);
        $binder->bind('stubProcessorResolver')
               ->named('net.stubbles.websites.processor.defaultResolver')
               ->toInstance($this->createProcessorResolver($binder->getInjector()));
        if (false === $this->authEnabled) {
            $binder->bind('stubProcessorResolver')
                   ->toProviderClass('net::stubbles::websites::ioc::stubProcessorResolverProvider');
        } else {
            $binder->bind('stubProcessorResolver')
                   ->named('net.stubbles.websites.processor.finalResolver')
                   ->toProviderClass('net::stubbles::websites::ioc::stubProcessorResolverProvider');
            $binder->bind('stubProcessorResolver')
                   ->to('net::stubbles::websites::processors::auth::stubAuthProcessorResolver');
        }
        
        foreach ($this->routerClasses as $paramValue => $routerClass) {
            if (null != $routerClass) {
                $binder->bind('stubRouter')
                       ->named($paramValue)
                       ->to($routerClass);
            }
        }
        
        if (isset($this->processors['xml']) === true) {
            $binder->bind('stubRouteReader')
                   ->to('net::stubbles::webapp::xml::route::stubBcRouteReader');
            $binder->bind('stubSkinGenerator')
                   ->named('webapp.xml.skin.default')
                   ->to('net::stubbles::websites::xml::skin::stubDefaultSkinGenerator');
            $binder->bind('stubSkinGenerator')
                   ->named('webapp.xml.skin.cached')
                   ->to('net::stubbles::websites::xml::skin::stubCachingSkinGenerator');
            $binder->bind('stubSkinGenerator')
                   ->toProviderClass('net::stubbles::websites::xml::skin::stubSkinGeneratorProvider');
            $binder->bindConstant()
                   ->named('net.stubbles.webapp.xml.generators')
                   ->to($this->xmlGenerators);
        }
    }

    /**
     * returns the processor resolver according to configured processors
     *
     * @param   stubInjector           $injector
     * @return  stubProcessorResolver
     */
    protected function createProcessorResolver(stubInjector $injector)
    {
        if (count($this->processors) > 1) {
            stubClassLoader::load('net::stubbles::websites::processors::stubDefaultProcessorResolver');
            $processorResolver = new stubDefaultProcessorResolver($injector,
                                                                  $this->defaultProcessorParamValue,
                                                                  $this->processors[$this->defaultProcessorParamValue],
                                                                  $this->interceptorDescriptors[$this->defaultProcessorParamValue]
                                 );
            foreach ($this->processors as $paramValue => $processor) {
                if ($paramValue != $this->defaultProcessorParamValue) {
                    $processorResolver->addProcessor($paramValue,
                                                     $processor,
                                                     $this->interceptorDescriptors[$paramValue]
                    );
                }
            }
        } else {
            stubClassLoader::load('net::stubbles::websites::processors::stubSimpleProcessorResolver');
            $processorResolver = new stubSimpleProcessorResolver($injector,
                                                                 $this->processors[$this->defaultProcessorParamValue],
                                                                 $this->interceptorDescriptors[$this->defaultProcessorParamValue]
                                 );
        }
        
        return $processorResolver;
    }
}
?>