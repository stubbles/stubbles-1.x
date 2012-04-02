<?php
/**
 * Binder for the IoC functionality.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBinder.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::ioc::stubInjector',
                      'net::stubbles::ioc::stubBindingScope',
                      'net::stubbles::ioc::stubBindingScopes'
);
/**
 * Binder for the IoC functionality.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubBinder extends stubBaseObject
{
    /**
     * Injector used by this binder
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * Create a new binder
     *
     * @param  stubInjector       $injector  optional
     */
    public function __construct(stubInjector $injector = null)
    {
        if (null === $injector) {
            $this->injector = new stubInjector();
        } else {
            $this->injector = $injector;
        }
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   stubSession  $session
     * @return  stubBinder
     * @since   1.5.0
     */
    public function setSessionForSessionScope(stubSession $session)
    {
        $this->injector->setSessionForSessionScope($session);
        return $this;
    }

    /**
     * Bind a new interface to a class
     *
     * @param   string  $interface
     * @return  stubClassBinding
     */
    public function bind($interface)
    {
        return $this->injector->bind($interface);
    }

    /**
     * Bind a new constant
     *
     * @return  stubConstantBinding
     */
    public function bindConstant()
    {
        return $this->injector->bindConstant();
    }

    /**
     * Get an injector for this binder
     *
     * @return  stubInjector
     */
    public function getInjector()
    {
        return $this->injector;
    }
}
?>