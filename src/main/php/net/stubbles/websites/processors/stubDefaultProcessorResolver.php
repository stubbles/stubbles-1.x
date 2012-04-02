<?php
/**
 * Default implementation for the processor resolver.
 *
 * @package     stubbles
 * @subpackage  websites_processors
 * @version     $Id: stubDefaultProcessorResolver.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubAbstractProcessorResolver');
/**
 * Default implementation for the processor resolver.
 *
 * The default processor resolver is able to select the processor to be used
 * for the current request depending on the request parameter <em>processor</em>.
 * For instance, if you add two processors with
 * <code>
 *   $defaultProcessor->addProcessor('foo', 'org::stubbles::test::FooProcessor');
 *   $defaultProcessor->addProcessor('bar', 'org::stubbles::test::BarProcessor');
 * </code>
 * then the first processor class will be selected if the value of the request
 * param is <em>foo</em>.
 *
 * If the parameter is not set or invalid it will fallback to the default
 * processor set with the constructor.
 *
 * @package     stubbles
 * @subpackage  websites_processors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubDefaultProcessorResolver extends stubAbstractProcessorResolver
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
    protected $processors             = array();
    /**
     * list of interceptor descriptors
     *
     * @var  array<string,string>
     */
    protected $interceptorDescriptors = array();

    /**
     * constructor
     *
     * The processor given here will become the default processor to be used
     * if no processor or an invalid one is choosen.
     *
     * @param  stubInjector  $injector                   injector
     * @param  string        $paramValue                 value of the request parameter that identifies this processor
     * @param  string        $defaultProcessorClassName  full qualified class name of the processor
     * @param  string        $interceptorDescriptor      optional  the interceptor descriptor
     */
    public function __construct(stubInjector $injector, $paramValue, $defaultProcessorClassName, $interceptorDescriptor = null)
    {
        $this->injector                   = $injector;
        $this->defaultProcessorParamValue = $paramValue;
        $this->addProcessor($paramValue, $defaultProcessorClassName, $interceptorDescriptor);
    }

    /**
     * adds a processor to the list of available processors
     * 
     * @param  string  $paramValue             value of the request parameter that identifies this processor
     * @param  string  $fqClassName            full qualified class name of the processor
     * @param  string  $interceptorDescriptor  optional  the interceptor descriptor
     */
    public function addProcessor($paramValue, $fqClassName, $interceptorDescriptor = null)
    {
        $this->processors[$paramValue]             = $fqClassName;
        $this->interceptorDescriptors[$paramValue] = ((null == $interceptorDescriptor) ? ('interceptors') : ($interceptorDescriptor));
    }

    /**
     * returns interceptor descriptor
     *
     * @param   stubRequest  $request  the current request
     * @return  string
     */
    public function getInterceptorDescriptor(stubRequest $request)
    {
        $paramValue = $request->readParam('processor')->ifIsOneOf(array_keys($this->processors), $this->defaultProcessorParamValue);
        return $this->interceptorDescriptors[$paramValue];
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
        $paramValue = $request->readParam('processor')->ifIsOneOf(array_keys($this->processors), $this->defaultProcessorParamValue);
        $session->putValue('net.stubbles.websites.lastProcessor', $paramValue);
        return $this->processors[$paramValue];
    }
}
?>