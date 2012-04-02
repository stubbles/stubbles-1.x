<?php
/**
 * Extended Reflection class for classes that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionObject.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
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
class stubReflectionObject extends ReflectionObject implements stubBaseReflectionClass
{
    /**
     * name of the reflected class
     *
     * @var  string
     */
    protected $className;
    /**
     * instance of the reflected class
     *
     * @var  object
     */
    protected $classObject;

    /**
     * constructor
     *
     * @param  object  $classObject  instance of class to reflect
     */
    public function __construct($classObject)
    {
        parent::__construct($classObject);
        $this->className   = get_class($classObject);
        $this->classObject = $classObject;
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
        if (($compare instanceof self) == false) {
            return false;
        }
        
        if ($compare->classObject instanceof stubObject) {
            $compareHashCode = $compare->classObject->hashCode();
        } else {
            $compareHashCode = spl_object_hash($compare->classObject);
        }
        
        if ($this->classObject instanceof stubObject) {
            $classHashCode = $this->classObject->hashCode();
        } else {
            $classHashCode = spl_object_hash($this->classObject);
        }
        
        return ($compare->className == $this->className && $compareHashCode == $classHashCode);
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionObject['[name-of-reflected-class]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionObject[MyClass] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionObject[' . $this->className . "] {\n}\n";
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
     * returns the instance of the class with with this reflection instance was created
     *
     * @return  object
     */
    public function getObjectInstance()
    {
        return $this->classObject;
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
            $stubRefClasses[] = new stubReflectionClass($interface->getName());
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
        
        $stubRefClass = new stubReflectionClass($parentClass->getName());
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