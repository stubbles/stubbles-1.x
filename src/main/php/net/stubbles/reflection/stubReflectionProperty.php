<?php
/**
 * Extended Reflection class for class properties that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionProperty.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Extended Reflection class for class properties that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionProperty extends ReflectionProperty implements stubAnnotatable
{
    /**
     * Name of the class
     *
     * @var  string
     */
    protected $className;
    /**
     * reflection instance for class declaring this property
     *
     * @var  stubBaseReflectionClass
     */
    protected $refClass;
    /**
     * Name of the property
     *
     * @var  string
     */
    protected $propertyName;

    /**
     * constructor
     *
     * @param  string|stubBaseReflectionClass  $class         name of class to reflect
     * @param  string                          $propertyName  name of property to reflect
     */
    public function __construct($class, $propertyName)
    {
        if ($class instanceof stubBaseReflectionClass) {
            $refClass  = $class;
            $className = $class->getName();
        } else {
            $refClass  = null;
            $className = $class;
        }

        parent::__construct($className, $propertyName);
        $this->refClass     = $refClass;
        $this->className    = $className;
        $this->propertyName = $propertyName;
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return stubAnnotationFactory::has($this->getDocComment(), $annotationName, stubAnnotation::TARGET_PROPERTY, $this->className . '::' . $this->propertyName, $this->getDeclaringClass()->getFileName());
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
        return stubAnnotationFactory::create($this->getDocComment(), $annotationName, stubAnnotation::TARGET_PROPERTY, $this->className . '::' . $this->propertyName, $this->getDeclaringClass()->getFileName());
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
            return ($compare->className == $this->className && $compare->propertyName == $this->propertyName);
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionProperty['[name-of-reflected-class]'::'[name-of-reflected-property]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionProperty[MyClass::myProperty] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionProperty[' . $this->className . '::' . $this->propertyName . "] {\n}\n";
    }

    /**
     * returns the class that declares this parameter
     *
     * @return  stubBaseReflectionClass
     */
    public function getDeclaringClass()
    {
        $refClass = parent::getDeclaringClass();
        if ($refClass->getName() === $this->className) {
            if (null === $this->refClass) {
                $this->refClass = new stubReflectionClass($this->className);
            }
            
            return $this->refClass;
        }
        
        $stubRefClass = new stubReflectionClass($refClass->getName());
        return $stubRefClass;
    }
}
?>