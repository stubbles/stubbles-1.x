<?php
/**
 * Injector for the IoC functionality.
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubInjector.php 3073 2011-02-28 22:28:42Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinding',
                      'net::stubbles::ioc::stubBindingException',
                      'net::stubbles::ioc::stubBindingScopes',
                      'net::stubbles::ioc::stubClassBinding',
                      'net::stubbles::ioc::stubConstantBinding',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::reflection::stubReflectionParameter'
);
/**
 * Injector for the IoC functionality.
 *
 * Used to create the instances.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubInjector extends stubBaseObject implements stubClonable
{
    /**
     * list of available binding scopes
     *
     * @var  stubBindingScopes
     */
    protected $scopes;
    /**
     * bindings used by the injector that are not yet in the index
     *
     * @var  array<stubBinding>
     */
    protected $bindings   = array();
    /**
     * index for faster access to bindings
     *
     * Do not access this array directly, use getIndex() instead. The binding
     * index is a requirement because the key for a binding is not necessarily
     * complete when the binding is added to the injector.
     *
     * @var  array<string,stubBinding>
     * @see  stubInjector::getIndex()
     */
    private $bindingIndex = array();

    /**
     * constructor
     *
     * @param  stubBindingScopes  $scopes  optional
     * @since  1.5.0
     */
    public function __construct(stubBindingScopes $scopes = null)
    {
        if (null === $scopes) {
            $this->scopes = new stubBindingScopes();
        } else {
            $this->scopes = $scopes;
        }
    }

    /**
     * sets session to be used with the session scope
     *
     * @param   stubSession   $session
     * @return  stubInjector
     * @since   1.5.0
     */
    public function setSessionForSessionScope(stubSession $session)
    {
        $this->scopes->setSessionForSessionScope($session);
        return $this;
    }

    /**
     * adds a new binding to the injector
     *
     * @param   stubBinding  $binding
     * @return  stubBinding
     */
    public function addBinding(stubBinding $binding)
    {
        $this->bindings[] = $binding;
        return $binding;
    }

    /**
     * creates and adds a class binding
     *
     * @param   string            $interface
     * @return  stubClassBinding
     * @since   1.5.0
     */
    public function bind($interface)
    {
        return $this->addBinding(new stubClassBinding($this,
                                                      $interface,
                                                      $this->scopes
                                 )
               );
    }

    /**
     * creates and adds a constanct binding
     *
     * @return  stubConstantBinding
     * @since   1.5.0
     */
    public function bindConstant()
    {
        return $this->addBinding(new stubConstantBinding($this));
    }

    /**
     * check whether a binding for a type is available (explicit and implicit)
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasBinding($type, $name = null)
    {
        return ($this->getBinding($type, $name) != null);
    }

    /**
     * check whether an excplicit binding for a type is available
     *
     * Be aware that implicit bindings turn into explicit bindings when
     * hasBinding() or getInstance() are called.
     *
     * @param   string   $type
     * @param   string   $name
     * @return  boolean
     */
    public function hasExplicitBinding($type, $name = null)
    {
        $bindingIndex = $this->getIndex();
        if (null !== $name) {
            if (isset($bindingIndex[$type . '#' . $name]) === true) {
                return true;
            }
        }

        return isset($bindingIndex[$type]);
    }

    /**
     * get an instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  object
     * @throws  stubBindingException
     */
    public function getInstance($type, $name = null)
    {
        $binding = $this->getBinding($type, $name);
        if (null === $binding) {
            throw new stubBindingException('No binding for ' . $type . ' defined');
        }
        
        return $binding->getInstance($type, $name);
    }

    /**
     * check whether a constant is available
     *
     * There is no need to distinguish between explicit and implicit binding for
     * constant bindings as there are only explicit constant bindings and never
     * implicit ones.
     *
     * @param   string  $name  name of constant to check for
     * @return  bool
     * @since   1.1.0
     */
    public function hasConstant($name)
    {
        return $this->hasBinding(stubConstantBinding::TYPE, $name);
    }

    /**
     * returns constanct value
     *
     * @param   string  $name  name of constant value to retrieve
     * @return  scalar
     * @since   1.1.0
     */
    public function getConstant($name)
    {
        return $this->getInstance(stubConstantBinding::TYPE, $name);
    }

    /**
     * returns the binding for a name and type
     *
     * @param   string       $type
     * @param   string       $name
     * @return  stubBinding
     */
    protected function getBinding($type, $name = null)
    {
        $bindingIndex = $this->getIndex();
        if (null !== $name) {
            if (isset($bindingIndex[$type . '#' . $name]) === true) {
                return $bindingIndex[$type . '#' . $name];
            }
        }
        
        if (isset($bindingIndex[$type]) === true) {
            return $bindingIndex[$type];
        }
        
        // prevent illegal access to reflection class for constant type
        if (stubConstantBinding::TYPE === $type) {
            return null;
        }
        
        // check for default implementation
        $typeClass = new stubReflectionClass($type);
        if ($typeClass->hasAnnotation('ImplementedBy') === true) {
            return $this->bind($type)
                        ->to($typeClass->getAnnotation('ImplementedBy')
                                       ->getDefaultImplementation()
                          );
        } elseif ($typeClass->hasAnnotation('ProvidedBy') === true) {
            return $this->bind($type)
                        ->toProviderClass($typeClass->getAnnotation('ProvidedBy')
                                                    ->getProviderClass()
                          );
        }

        // try implicit binding
        if ($typeClass->isInterface() === false) {
            return $this->bind($type)
                        ->to($typeClass);
        }
        
        return null;
    }

    /**
     * returns the binding index
     *
     * @return  array<string,stubBinding>
     */
    protected function getIndex()
    {
        if (empty($this->bindings) === true) {
            return $this->bindingIndex;
        }
        
        foreach ($this->bindings as $binding) {
            $this->bindingIndex[$binding->getKey()] = $binding;
        }
        
        $this->bindings = array();
        return $this->bindingIndex;
    }

    /**
     * handle injections for given instance
     *
     * @param   object                   $instance
     * @param   stubBaseReflectionClass  $class     optional
     * @throws  stubBindingException
     */
    public function handleInjections($instance, stubBaseReflectionClass $class = null)
    {
        if (null === $class) {
            $class = new stubReflectionClass(get_class($instance));
        }
        
        foreach ($class->getMethods() as $method) {
            /* @var  $method  stubReflectionMethod */
            if ($method->isPublic() === false || strncmp($method->getName(), '__', 2) === 0 || $method->hasAnnotation('Inject') === false) {
                continue;
            }

            try {
                $paramValues = $this->getInjectionValuesForMethod($method, $class);
            } catch (stubBindingException $be) {
                if ($method->getAnnotation('Inject')->isOptional() === false) {
                    throw $be;
                }
                
                continue;
            }

            $method->invokeArgs($instance, $paramValues);
        }
    }

    /**
     * returns a list of all injection values for given method
     *
     * @param   stubReflectionMethod     $method
     * @param   stubBaseReflectionClass  $class
     * @return  array<mixed>
     * @throws  stubBindingException
     */
    public function getInjectionValuesForMethod(stubReflectionMethod $method, stubBaseReflectionClass $class)
    {
        $paramValues = array();
        $namedMethod = (($method->hasAnnotation('Named') === true) ? ($method->getAnnotation('Named')->getName()) : (null));
        foreach ($method->getParameters() as $param) {
            /* @var  $param  stubReflectionParameter */
            $paramClass = $param->getClass();
            $type       = ((null !== $paramClass) ? ($paramClass->getName()) : (stubConstantBinding::TYPE));
            $name       = (($param->hasAnnotation('Named') === true) ? ($param->getAnnotation('Named')->getName()) : ($namedMethod));
            if ($this->hasExplicitBinding($type, $name) === false && $method->getAnnotation('Inject')->isOptional() === true) {
                // Somewhat hackish... throwing an exception here which is catched and ignored in handleInjections()
                throw new stubBindingException('Could not find explicit binding for optional injection of type ' . $this->createTypeMessage($type, $name) . ' to complete  ' . $this->createCalledMethodMessage($class, $method, $param, $type));
            }
            
            if ($this->hasBinding($type, $name) === false) {
                $typeMsg = $this->createTypeMessage($type, $name);
                throw new stubBindingException('Can not inject into ' . $this->createCalledMethodMessage($class, $method, $param, $type)  . '. No binding for type ' . $typeMsg . ' specified.');
            }
            
            $paramValues[] = $this->getInstance($type, $name);
        }
        
        return $paramValues;
    }

    /**
     * creates the complete type message
     *
     * @param   string  $type  type to create message for
     * @param   string  $name  name of named parameter
     * @return  string
     */
    protected function createTypeMessage($type, $name)
    {
        return ((null !== $name) ? ($type . ' (named "' . $name . '")') : ($type));
    }

    /**
     * creates the called method message
     *
     * @param   stubBaseReflectionClass  $class
     * @param   stubReflectionMethod     $method
     * @param   stubReflectionParameter  $parameter
     * @param   string                   $type
     * @return  string
     */
    protected function createCalledMethodMessage(stubBaseReflectionClass $class, stubReflectionMethod $method, stubReflectionParameter $parameter, $type)
    {
        $message = $class->getFullQualifiedClassName() . '::' . $method->getName() . '(';
        if (stubConstantBinding::TYPE !== $type) {
            $message .= $type . ' ';
        } elseif ($parameter->isArray() === true) {
            $message .= 'array ';
        }
        
        return $message . '$' . $parameter->getName() . ')';
    }
}
?>