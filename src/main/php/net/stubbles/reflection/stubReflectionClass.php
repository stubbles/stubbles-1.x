<?php
/**
 * Extended Reflection class for classes that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionClass.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubBaseReflectionClass',
                      'net::stubbles::reflection::stubReflectionExtension',
                      'net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::reflection::stubReflectionProperty'
);
/**
 * Extended Reflection class for classes that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionClass extends ReflectionClass implements stubBaseReflectionClass
{
    /**
     * name of the reflected class
     *
     * @var  string
     */
    protected $className;

    /**
     * constructor
     *
     * @param   string  $className  name of class to reflect
     * @throws  ReflectionException
     */
    public function __construct($className)
    {
        if (strstr($className, '::') !== false || strstr($className, '.') !== false) {
            $nqClassName = stubClassLoader::getNonQualifiedClassName($className);
            if (class_exists($nqClassName, false) === false) {
                stubClassLoader::load($className);
            }
            
            $className = $nqClassName;
        } elseif (class_exists($className, false) === false && interface_exists($className, false) === false) {
            // prevent autoload from being called by the internal reflection class
            throw new ReflectionException('Class ' . $className . ' does not exist');
        }
        
        parent::__construct($className);
        $this->className = $className;
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return stubAnnotationFactory::has($this->getDocComment(), $annotationName, stubAnnotation::TARGET_CLASS, $this->className, $this->getFileName());
    }

    /**
     * return the specified annotation
     *
     * @param   string          $annotationName
     * @return  stubAnnotation
     * @throws  ReflectionException
     */
    public function getAnnotation($annotationName)
    {
        return stubAnnotationFactory::create($this->getDocComment(), $annotationName, stubAnnotation::TARGET_CLASS, $this->className, $this->getFileName());
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->className == $this->className);
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionClass['[name-of-reflected-class]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionClass[MyClass] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionClass[' . $this->className . "] {\n}\n";
    }

    /**
     * returns the full qualified class name of the reflected class
     * 
     * If the class has not been loaded with stubClassLoader the non qualified
     * class name will be returned.
     *
     * @return  string
     */
    public function getFullQualifiedClassName()
    {
        $fqClassName = stubClassLoader::getFullQualifiedClassName($this->className);
        if (null == $fqClassName) {
            return $this->className;
        }
        
        return $fqClassName;
    }

    /**
     * returns the constructor or null if none exists
     * 
     * Warning: PHP4-style constructors are not supported! If you have one use
     * getMethod($className) instead to retrieve the constructor reflection method.
     *
     * @return  stubReflectionMethod
     */
    public function getConstructor()
    {
        return $this->getMethod('__construct');
    }

    /**
     * returns the specified method or null if it does not exist
     *
     * @param   string                $name  name of method to return
     * @return  stubReflectionMethod
     */
    public function getMethod($name)
    {
        if (parent::hasMethod($name) == false) {
            return null;
        }
        
        $stubRefMethod = new stubReflectionMethod($this, $name);
        return $stubRefMethod;
    }

    /**
     * returns a list of all methods
     *
     * @return  array<stubReflectionMethod>
     */
    public function getMethods()
    {
        $methods    = parent::getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            $stubMethods[] = new stubReflectionMethod($this, $method->getName());
        }
        
        return $stubMethods;
    }

    /**
     * returns a list of all methods which satify the given matcher
     *
     * @param   stubMethodMatcher            $methodMatcher
     * @return  array<stubReflectionMethod>
     */
    public function getMethodsByMatcher(stubMethodMatcher $methodMatcher)
    {
        $methods     = parent::getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            if ($methodMatcher->matchesMethod($method) === true) {
                $stubMethod = new stubReflectionMethod($this, $method->getName());
                if ($methodMatcher->matchesAnnotatableMethod($stubMethod) === true) {
                    $stubMethods[] = $stubMethod;
                }
            }
        }
        
        return $stubMethods;
    }

    /**
     * returns the specified property or null if it does not exist
     *
     * @param   string                  $name  name of property to return
     * @return  stubReflectionProperty
     */
    public function getProperty($name)
    {
        if (parent::hasProperty($name) == false) {
            return null;
        }
        
        $stubRefProperty = new stubReflectionProperty($this, $name);
        return $stubRefProperty;
    }

    /**
     * returns a list of all properties
     *
     * @return  array<stubReflectionProperty>
     */
    public function getProperties()
    {
        $properties     = parent::getProperties();
        $stubProperties = array();
        foreach ($properties as $property) {
            $stubProperties[] = new stubReflectionProperty($this, $property->getName());
        }
        
        return $stubProperties;
    }

    /**
     * returns a list of all properties which satify the given matcher
     *
     * @param   stubPropertyMatcher            $propertyMatcher
     * @return  array<stubReflectionProperty>
     */
    public function getPropertiesByMatcher(stubPropertyMatcher $propertyMatcher)
    {
        $properties     = parent::getProperties();
        $stubProperties = array();
        foreach ($properties as $property) {
            if ($propertyMatcher->matchesProperty($property) === true) {
                $stubProperty = new stubReflectionProperty($this, $property->getName());
                if ($propertyMatcher->matchesAnnotatableProperty($stubProperty) === true) {
                    $stubProperties[] = $stubProperty;
                }
            }
        }
        
        return $stubProperties;
    }

    /**
     * returns a list of all interfaces
     *
     * @return  array<stubReflectionClass>
     */
    public function getInterfaces()
    {
        $interfaces     = parent::getInterfaces();
        $stubRefClasses = array();
        foreach ($interfaces as $interface) {
            $stubRefClasses[] = new self($interface->getName());
        }
        
        return $stubRefClasses;
    }

    /**
     * returns a list of all interfaces
     *
     * @return  stubReflectionClass
     */
    public function getParentClass()
    {
        $parentClass  = parent::getParentClass();
        if (null === $parentClass || false === $parentClass) {
            return null;
        }
        
        $stubRefClass = new self($parentClass->getName());
        return $stubRefClass;
    }

    /**
     * returns the extension to where this class belongs too
     *
     * @return  stubReflectionExtension
     */
    public function getExtension()
    {
        $extensionName  = $this->getExtensionName();
        if (null === $extensionName || false === $extensionName) {
            return null;
        }
        
        $stubRefExtension = new stubReflectionExtension($extensionName);
        return $stubRefExtension;
    }

    /**
     * returns the package where the class belongs to
     *
     * @return  stubReflectionPackage
     */
    public function getPackage()
    {
        $refPackage = new stubReflectionPackage(stubClassLoader::getPackageName($this->getFullQualifiedClassName()));
        return $refPackage;
    }

    /**
     * checks whether the type is an object
     *
     * @return  bool
     */
    public function isObject()
    {
        return true;
    }

    /**
     * checks whether the type is a primitive
     *
     * @return  bool
     */
    public function isPrimitive()
    {
        return false;
    }
}
?>