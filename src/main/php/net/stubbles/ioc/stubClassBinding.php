<?php
/**
 * Binding to bind an interface to an implementation.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubClassBinding.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinding',
                      'net::stubbles::ioc::stubBindingException',
                      'net::stubbles::ioc::stubBindingScope',
                      'net::stubbles::ioc::stubBindingScopes',
                      'net::stubbles::ioc::stubDefaultInjectionProvider',
                      'net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::reflection::stubBaseReflectionClass',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Binding to bind an interface to an implementation.
 *
 * Please note that you can do a binding to a class or to an instance, or to an
 * injection provider, or to an injection provider class. These options are
 * mutually exclusive and have a predictive order:
 * 1. Instance
 * 2. Provider instance
 * 3. Provider class
 * 4. Concrete implementation class
 * 
 * @package     stubbles
 * @subpackage  ioc
 */
class stubClassBinding extends stubBaseObject implements stubBinding
{
    /**
     * injector used by this binding
     *
     * @var  stubInjector
     */
    protected $injector      = null;
    /**
     * type for this binding
     *
     * @var  string
     */
    protected $type          = null;
    /**
     * class that implements this binding
     *
     * @var  stubReflectionClass
     */
    protected $impl          = null;
    /**
     * Annotated with a name
     *
     * @var  string
     */
    protected $name          = null;
    /**
     * scope of the binding
     *
     * @var  stubBindingScope
     */
    protected $scope         = null;
    /**
     * instance this type is bound to
     *
     * @var  object
     */
    protected $instance      = null;
    /**
     * provider to use for this binding
     *
     * @var  stubInjectionProvider
     */
    protected $provider      = null;
    /**
     * provider class to use for this binding (will be created via injector)
     *
     * @var  string
     */
    protected $providerClass = null;
    /**
     * list of available binding scopes
     *
     * @var  stubBindingScopes
     */
    protected $scopes;

    /**
     * constructor
     *
     * @param  stubInjector       $injector
     * @param  string             $type
     * @param  stubBindingScopes  $scopes
     */
    public function __construct(stubInjector $injector, $type, stubBindingScopes $scopes)
    {
        $this->injector = $injector;
        $this->type     = $type;
        $this->impl     = $type;
        $this->scopes   = $scopes;
    }

    /**
     * set the concrete implementation
     *
     * @param   stubBaseReflectionClass|string  $impl
     * @return  stubClassBinding
     * @throws  stubIllegalArgumentException
     */
    public function to($impl)
    {
        if (is_string($impl) === false && ($impl instanceof stubBaseReflectionClass) === false) {
            throw new stubIllegalArgumentException('$impl must be a string or an instance of net::stubbles::reflection::stubBaseReflectionClass');
        }
        
        $this->impl = $impl;
        return $this;
    }

    /**
     * set the concrete instance
     *
     * This cannot be used in conjuction with the 'toProvider()' or
     * 'toProviderClass()' method.
     *
     * @param   object            $instance
     * @return  stubClassBinding
     * @throws  stubIllegalArgumentException
     */
    public function toInstance($instance)
    {
        if (($instance instanceof $this->type) === false) {
            throw new stubIllegalArgumentException('Instance of ' . $this->type . ' expectected, ' . get_class($instance) . ' given.');
        }
        
        $this->instance = $instance;
        return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProviderClass()' method.
     *
     * @param   stubInjectionProvider  $provider
     * @return  stubClassBinding
     */
    public function toProvider(stubInjectionProvider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'toInstance()' or
     * 'toProvider()' method.
     *
     * @param   string|stubBaseReflectionClass  $providerClass
     * @return  stubClassBinding
     */
    public function toProviderClass($providerClass)
    {
        if ($providerClass instanceof stubBaseReflectionClass) {
            $this->providerClass = $providerClass->getFullQualifiedClassName();
        } else {
            $this->providerClass = $providerClass;
        }

        return $this;
    }

    /**
     * binds the class to the singleton scope
     *
     * @return  stubClassBinding
     * @since   1.5.0
     */
    public function asSingleton()
    {
        $this->scope = $this->scopes->getSingletonScope();
        return $this;
    }

    /**
     * binds the class to the session scope
     *
     * @return  stubClassBinding
     * @since   1.5.0
     */
    public function inSession()
    {
        $this->scope = $this->scopes->getSessionScope();
        return $this;
    }

    /**
     * set the scope
     *
     * @param   stubBindingScope  $scope
     * @return  stubClassBinding
     */
    public function in(stubBindingScope $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * Set the name of the injection
     *
     * @param   string            $name
     * @return  stubClassBinding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns the created instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  stubBindingException
     */
    public function getInstance($type, $name)
    {
        if (null !== $this->instance) {
            return $this->instance;
        }
        
        if (is_string($this->impl) === true) {
            $this->impl = new stubReflectionClass($this->impl);
        }

        if (null === $this->scope) {
            if ($this->impl->hasAnnotation('Singleton') === true) {
                $this->scope = $this->scopes->getSingletonScope();
            }
        }
        
        if (null === $this->provider) {
            if (null != $this->providerClass) {
                $provider = $this->injector->getInstance($this->providerClass);
                if (($provider instanceof stubInjectionProvider) === false) {
                    throw new stubBindingException('Configured provider class ' . $this->providerClass . ' for type ' . $this->type . ' is not an instance of net::stubbles::ioc::stubInjectionProvider.');
                }
                
                $this->provider = $provider;
            } else {
                $this->provider = new stubDefaultInjectionProvider($this->injector, $this->impl);
            }
        }

        if (null !== $this->scope) {
            return $this->scope->getInstance(new stubReflectionClass($this->type), $this->impl, $this->provider);
        }
        
        return $this->provider->get($name);
    }

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        if (null === $this->name) {
            return $this->type;
        }
        
        return $this->type . '#' . $this->name;
    }
}
?>