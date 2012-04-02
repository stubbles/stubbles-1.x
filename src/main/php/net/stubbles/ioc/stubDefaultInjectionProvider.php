<?php
/**
 * Default injection provider.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubDefaultInjectionProvider.php 2239 2009-06-16 19:25:52Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::ioc::stubInjector'
);
/**
 * Default injection provider.
 *
 * Creates objects and injects all dependencies via the default injector.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubDefaultInjectionProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * injector to use for dependencies
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * concrete implementation to use
     *
     * @var  stubBaseReflectionClass
     */
    protected $impl;

    /**
     * constructor
     *
     * @param  stubInjector             $injector
     * @param  stubBaseReflectionClass  $impl
     */
    public function __construct(stubInjector $injector, stubBaseReflectionClass $impl)
    {
        $this->injector = $injector;
        $this->impl     = $impl;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        $constructor = $this->impl->getConstructor();
        if (null === $constructor || $constructor->hasAnnotation('Inject') === false) {
            $instance = $this->impl->newInstance();
        } else {
            $instance = $this->impl->newInstanceArgs($this->injector->getInjectionValuesForMethod($constructor, $this->impl));
        }

        $this->injector->handleInjections($instance, $this->impl);
        return $instance;
    }
}
?>