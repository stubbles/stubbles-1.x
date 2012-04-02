<?php
/**
 * Extended Reflection class for functions that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionFunction.php 2989 2011-02-11 18:35:45Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::stubReflectionParameter',
                      'net::stubbles::reflection::stubReflectionPrimitive',
                      'net::stubbles::reflection::stubReflectionRoutine'
);
/**
 * Extended Reflection class for functions that allows usage of annotations.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionFunction extends ReflectionFunction implements stubReflectionRoutine
{
    /**
     * name of the reflected function
     *
     * @var  string
     */
    protected $functionName;
    /**
     * docblock comment for this class
     *
     * @var  string
     */
    protected $docComment;

    /**
     * constructor
     *
     * @param  string  $functionName  name of function to reflect
     */
    public function __construct($functionName)
    {
        parent::__construct($functionName);
        $this->functionName = $functionName;
        $this->docComment   = $this->getDocComment();
    }

    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return stubAnnotationFactory::has($this->docComment, $annotationName, stubAnnotation::TARGET_FUNCTION, $this->functionName, $this->getFileName());
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
        return stubAnnotationFactory::create($this->docComment, $annotationName, stubAnnotation::TARGET_FUNCTION, $this->functionName, $this->getFileName());
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
            return ($compare->functionName === $this->functionName);
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * 'net::stubbles::reflection::stubReflectionFunction['[name-of-reflected-function]'()]  {}'
     * <code>
     * net::stubbles::reflection::stubReflectionFunction[fopen()] {
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionFunction[' . $this->functionName . "()] {\n}\n";
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
     * returns information about the return type of a function
     * 
     * If the return type is a class the return value is an instance of
     * stubReflectionClass (if the class is unknown a
     * stubClassNotFoundException will be thrown), if it is a scalar type the
     * return value is an instance of stubReflectionPrimitive, and if the
     * method does not have a return value this method returns null.
     * Please be aware that this is guessing from the doc block with which the
     * function is documented. If the doc block is missing or incorrect the
     * return value of this method may be wrong. This is due to missing type
     * hints for return values in PHP itself.
     *
     * @return  stubReflectionType
     */
    public function getReturnType()
    {
        $returnPart = strstr($this->docComment, '@return');
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