<?php
/**
 * Binding to bind a property to a constant value.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubConstantBinding.php 3086 2011-03-14 14:41:52Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinding',
                      'net::stubbles::ioc::stubBindingException',
                      'net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::reflection::stubBaseReflectionClass'
);
/**
 * Binding to bind a property to a constant value.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubConstantBinding extends stubBaseObject implements stubBinding
{
    /**
     * This string is used when generating the key for a constant binding.
     */
    const TYPE               = '__CONSTANT__';
    /**
     * injector used by this binding
     *
     * @var  stubInjector
     */
    protected $injector      = null;
    /**
     * annotated with a name
     *
     * @var  string
     */
    protected $name          = null;
    /**
     * value to provide
     *
     * @var  mixed
     */
    protected $value;
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
     * constructor
     *
     * @param  stubInjector  $injector
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * set the name of the injection
     *
     * @param   string               $name
     * @return  stubConstantBinding
     */
    public function named($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set the constant value
     *
     * @param   mixed                $value
     * @return  stubConstantBinding
     */
    public function to($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * set the provider that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProviderClass()' method.
     *
     * @param   stubInjectionProvider  $provider
     * @return  stubConstantBinding
     * @since   1.6.0
     */
    public function toProvider(stubInjectionProvider $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * set the provider class that should be used to create instances for this binding
     *
     * This cannot be used in conjuction with the 'to()' or
     * 'toProvider()' method.
     *
     * @param   string|stubBaseReflectionClass  $providerClass
     * @return  stubConstantBinding
     * @since   1.6.0
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
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey()
    {
        return self::TYPE . '#' . $this->name;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     * @throws  stubBindingException
     */
    public function getInstance($type, $name)
    {
        if (null !== $this->provider) {
            return $this->provider->get($name);
        }

        if (null != $this->providerClass) {
            $provider = $this->injector->getInstance($this->providerClass);
            if (($provider instanceof stubInjectionProvider) === false) {
                 throw new stubBindingException('Configured provider class ' . $this->providerClass . ' for constant ' . $this->name . ' is not an instance of net::stubbles::ioc::stubInjectionProvider.');
            }

            $this->provider = $provider;
            return $this->provider->get($name);
        }

        return $this->value;
    }
}
?>