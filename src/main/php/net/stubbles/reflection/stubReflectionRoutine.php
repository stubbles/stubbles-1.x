<?php
/**
 * Common base interface for methods and functions.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubReflectionRoutine.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable');
/**
 * Common base interface for methods and functions.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
interface stubReflectionRoutine extends stubAnnotatable
{
    /**
     * returns name of the routine
     *
     * @return  string
     */
    public function getName();

    /**
     * checks whether routine was declared by PHP or not
     *
     * @return  bool
     */
    public function isInternal();

    /**
     * checks whether routine was declared by user or not
     *
     * @return  bool
     */
    public function isUserDefined();

    /**
     * returns name of file where routine was declared
     *
     * @return  string
     */
    public function getFileName();

    /**
     * returns line where routine declaration starts
     *
     * @return  int
     */
    public function getStartLine();

    /**
     * returns line where routine declaration ends
     *
     * @return  int
     */
    public function getEndLine();

    /**
     * returns doc comment for routine
     *
     * @return  string
     */
    public function getDocComment();

    /**
     * returns a list of static variables declared in the routine
     *
     * @return  array
     */
    public function getStaticVariables();

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare);

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
    public function __toString();

    /**
     * returns a list of all parameters
     *
     * @return  array<stubReflectionParameter>
     */
    public function getParameters();

    /**
     * returns the number of parameters of the routine
     *
     * @return  int
     */
    public function getNumberOfParameters();

    /**
     * returns the number of parameters of the routine
     *
     * @return  int
     */
    public function getNumberOfRequiredParameters();

    /**
     * checks whether return value is returned by reference or not
     *
     * @return  bool
     */
    public function returnsReference();

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
    public function getReturnType();
}
?>