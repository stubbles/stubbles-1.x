<?php
/**
 * Class for initializing the interceptors from a property file.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubPropertyBasedInterceptorInitializer.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::interceptors::stubInterceptorInitializer',
                      'net::stubbles::lang::stubProperties'
);
/**
 * Class for initializing the interceptors from a property file.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @since       1.1.0
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
class stubPropertyBasedInterceptorInitializer extends stubBaseObject implements stubInterceptorInitializer
{
    /**
     * descriptor that identifies the initializer
     *
     * @var  string
     */
    protected $descriptor        = 'interceptors';
    /**
     * injector instance to create single interceptors with
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * path to config files
     *
     * @var  string
     */
    protected $configPath;
    /**
     * interceptor properties
     *
     * @var  stubProperties
     */
    protected $interceptorConfig;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @param  string        $configPath
     * @Inject
     * @Named{configPath}('net.stubbles.config.path')
     */
    public function  __construct(stubInjector $injector, $configPath)
    {
        $this->injector   = $injector;
        $this->configPath = $configPath;
    }

    /**
     * sets the descriptor that identifies the initializer
     *
     * @param   string                      $descriptor
     * @return  stubInterceptorInitializer
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
        return $this;
    }

    /**
     * initializing method
     *
     * @return  stubInitializer
     */
    public function init()
    {
        $this->interceptorConfig = stubProperties::fromFile($this->configPath . '/' . $this->descriptor . '.ini');
        return $this;
    }

    /**
     * returns the list of pre interceptors
     *
     * @return  array<stubPreInterceptor>
     * @throws  stubIllegalStateException
     */
    public function getPreInterceptors()
    {
        if (null === $this->interceptorConfig) {
            throw new stubIllegalStateException('Uninitialized state, call init() method first.');
        }

        return $this->getInterceptors('preinterceptors');
    }

    /**
     * returns the list of post interceptors
     *
     * @return  array<stubPostInterceptor>
     * @throws  stubIllegalStateException
     */
    public function getPostInterceptors()
    {
        if (null === $this->interceptorConfig) {
            throw new stubIllegalStateException('Uninitialized state, call init() method first.');
        }
        
        return $this->getInterceptors('postinterceptors');
    }

    /**
     * creates list of interceptor instances from list of interceptor class names
     * using the injector
     *
     * @param   string  $section
     * @return  array<stubPreInterceptor|stubPostInterceptor>
     */
    protected function getInterceptors($section)
    {
        $interceptors = array();
        foreach ($this->interceptorConfig->getSection($section) as $interceptorClassName) {
            $interceptors[] = $this->injector->getInstance($interceptorClassName);
        }

        return $interceptors;
    }
}
?>