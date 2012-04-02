<?php
/**
 * Extended Reflection class for parameters.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionParameter.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubReflectionFunction',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Extended Reflection class for parameters.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionParameter extends ReflectionParameter implements stubAnnotatable
{
    /**
     * name of reflected routine
     *
     * @var  string
     */
    protected $routineName;
    /**
     * reflection instance of routine containing this parameter
     *
     * @var  stubReflectionRoutine
     */
    protected $refRoutine;
    /**
     * name of reflected parameter
     *
     * @var  string
     */
    protected $paramName;

    /**
     * constructor
     *
     * @param  string|array|stubReflectionRoutine  $routine    name or reflection instance of routine
     * @param  string                              $paramName  name of parameter to reflect
     */
    public function __construct($routine, $paramName)
    {
        if ($routine instanceof stubReflectionMethod) {
            $refRoutine  = $routine;
            $routineName = array($routine->getDeclaringClass()->getName(), $routine->getName());
        } elseif ($routine instanceof stubReflectionFunction) {
            $refRoutine  = $routine;
            $routineName = $routine->getName();
        } else {
            $refRoutine  = null;
            $routineName = $routine;
        }
        
        parent::__construct($routineName, $paramName);
        $this->refRoutine  = $refRoutine;
        $this->routineName = $routineName;
        $this->paramName   = $paramName;
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        $refRoutine = $this->getRefRoutine();
        $targetName = ((is_array($this->routineName) === true) ? ($this->routineName[0] . '::' . $this->routineName[1] . '()') : ($this->routineName));
        return stubAnnotationFactory::has($refRoutine->getDocComment(), $annotationName . '#' . $this->paramName, stubAnnotation::TARGET_PARAM, $targetName, $refRoutine->getFileName());
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
        $refRoutine = $this->getRefRoutine();
        $targetName = ((is_array($this->routineName) === true) ? ($this->routineName[0] . '::' . $this->routineName[1] . '()') : ($this->routineName));
        return stubAnnotationFactory::create($refRoutine->getDocComment(), $annotationName . '#' . $this->paramName, stubAnnotation::TARGET_PARAM, $targetName, $refRoutine->getFileName());
    }

    /**
     * helper method to return the reflection routine defining this parameter
     *
     * @return  stubReflectionRoutine
     * @todo    replace by getDeclaringFunction() as soon as Stubbles requires at least PHP 5.2.3
     */
    protected function getRefRoutine()
    {
        if (null === $this->refRoutine) {
            if (is_array($this->routineName) === true) {
                $this->refRoutine = new stubReflectionMethod($this->routineName[0], $this->routineName[1]);
            } else {
                $this->refRoutine = new stubReflectionFunction($this->routineName);
            }
        }
        
        return $this->refRoutine;
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
        
        $class        = $this->getDeclaringClass();
        $compareClass = $compare->getDeclaringClass();
        if ((null == $class && null != $compareClass) || null != $class && null == $compareClass) {
            return false;
        }
        
        if (null == $class) {
            return ($compare->routineName == $this->routineName && $compare->paramName == $this->paramName);
        }
        
        return ($compareClass->getName() == $class->getName() && $compare->routineName == $this->routineName && $compare->paramName == $this->paramName);
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionParameter['[name-of-reflected-class]'::'[name-of-reflected-function]'(): Argument '[name-of-reflected-argument]']  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionParameter[MyClass::myMethod(): Argument foo] {
     * }
     * net::stubbles::reflection::stubReflectionParameter[myFunction(): Argument bar] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        if (is_array($this->routineName) == false) {
            return 'net::stubbles::reflection::stubReflectionParameter[' . $this->routineName . '(): Argument ' . $this->paramName . "] {\n}\n";
        }
        
        return 'net::stubbles::reflection::stubReflectionParameter[' . $this->routineName[0] . '::' . $this->routineName[1] . '(): Argument ' . $this->paramName . "] {\n}\n";
    }

    /**
     * returns the function that declares this parameter
     *
     * @return  stubReflectionFunction
     */
    # well, manual says its there, its even in php cvs, but calling
    # ReflectionParameter::getDeclaringFunction() results in a fatal error
    # with message "Call to undefined method"
    #public function getDeclaringFunction()
    #{
    #    $refFunction     = parent::getDeclaringFunction();
    #    $stubRefFunction = new stubReflectionFunction($refFunction->getName());
    #    return $stubRefFunction;
    #}

    /**
     * returns the class that declares this parameter
     *
     * @return  stubReflectionClass
     */
    public function getDeclaringClass()
    {
        if (is_array($this->routineName) === false) {
            return null;
        }
        
        $refClass     = parent::getDeclaringClass();
        $stubRefClass = new stubReflectionClass($refClass->getName());
        return $stubRefClass;
    }

    /**
     * returns the type (class) hint for this parameter
     *
     * @return  stubReflectionClass
     */
    public function getClass()
    {
        $refClass = parent::getClass();
        if (null === $refClass) {
            return null;
        }
        
        $stubRefClass = new stubReflectionClass($refClass->getName());
        return $stubRefClass;
    }
}
?>