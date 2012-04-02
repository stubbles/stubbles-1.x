<?php
/**
 * Extended Reflection class for class methods that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionMethod.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionParameter',
                      'net::stubbles::reflection::stubReflectionPrimitive',
                      'net::stubbles::reflection::stubReflectionRoutine'
);
/**
 * Extended Reflection class for class methods that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionMethod extends ReflectionMethod implements stubReflectionRoutine
{
    /**
     * name of the reflected class
     *
     * @var  string
     */
    protected $className;
    /**
     * declaring class
     *
     * @var  stubBaseReflectionClass
     */
    protected $refClass;
    /**
     * name of the reflected method
     *
     * @var  string
     */
    protected $methodName;

    /**
     * constructor
     *
     * @param  string|stubBaseReflectionClass  $class       name of class to reflect
     * @param  string                          $methodName  name of method to reflect
     */
    public function __construct($class, $methodName)
    {
        if ($class instanceof stubBaseReflectionClass) {
            $refClass   = $class;
            $className  = $refClass->getName();
        } else {
            $refClass   = null;
            $className  = $class;
        }

        parent::__construct($className, $methodName);
        $this->refClass   = $refClass;
        $this->className  = $className;
        $this->methodName = $methodName;
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return stubAnnotationFactory::has($this->getDocComment(), $annotationName, stubAnnotation::TARGET_METHOD, $this->className . '::' . $this->methodName . '()', $this->getFileName());
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
        return stubAnnotationFactory::create($this->getDocComment(), $annotationName, stubAnnotation::TARGET_METHOD, $this->className . '::' . $this->methodName . '()', $this->getFileName());
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
            return ($compare->className === $this->className && $compare->methodName === $this->methodName);
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionMethod['[name-of-reflected-class]'::'[name-of-reflected-method]'()]  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionMethod[MyClass::myMethod()] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionMethod[' . $this->className . '::' . $this->methodName . "()] {\n}\n";
    }

    /**
     * returns the class that declares this method
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

    /**
     * returns a list of all parameters
     *
     * @return  array<stubReflectionParameter>
     */
    public function getParameters()
    {
        $parameters     = parent::getParameters();
        $stubParameters = array();
        foreach ($parameters as $parameter) {
            $stubParameters[] = new stubReflectionParameter($this, $parameter->getName());
        }
        
        return $stubParameters;
    }

    /**
     * returns information about the return type of a method
     * 
     * If the return type is a class the return value is an instance of
     * stubReflectionClass (if the class is unknown a
     * stubClassNotFoundException will be thrown), if it is a scalar type the
     * return value is an instance of stubReflectionPrimitive, and if the
     * method does not have a return value this method returns null.
     * Please be aware that this is guessing from the doc block with which the
     * method is documented. If the doc block is missing or incorrect the return
     * value of this method may be wrong. This is due to missing type hints for
     * return values in PHP itself.
     *
     * @return  stubReflectionType
     */
    public function getReturnType()
    {
        $returnPart = strstr($this->getDocComment(), '@return');
        if (false === $returnPart) {
            return null;
        }
        
        $returnParts = explode(' ', trim(str_replace('@return', '', $returnPart)));
        $returnType  = trim($returnParts[0]);
        try {
            $reflectionType = stubReflectionPrimitive::forName(new ReflectionClass('stubReflectionPrimitive'), $returnType);
        } catch (stubIllegalArgumentException $iae) {
            $reflectionType = new stubReflectionClass($returnType);
        }
        
        return $reflectionType;
    }
}
?>