<?php
/**
 * Base exception class for all stubbles runtime exceptions.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 * @version     $Id: stubRuntimeException.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Base exception class for all stubbles runtime exceptions.
 *
 * A runtime exception should be thrown if a class is used in wrong way, e.g.
 * a missing configuration file or wrong class instance is supplied. Instances
 * of this and inherited exceptions should never be catched. The docblock of a
 * method must not indicate that a runtime exception may be thrown.
 * 
 * @package     stubbles
 * @subpackage  lang_exceptions
 */
class stubRuntimeException extends Exception implements stubThrowable
{
    /**
     * returns class informations
     *
     * @return  stubReflectionObject
     * @XMLIgnore
     */
    public function getClass()
    {
        stubClassLoader::load('net::stubbles::reflection::stubReflectionObject');
        $refObject = new stubReflectionObject($this);
        return $refObject;
    }

    /**
     * returns package informations
     *
     * @return  stubReflectionPackage
     * @XMLIgnore
     */
    public function getPackage()
    {
         stubClassLoader::load('net::stubbles::reflection::stubReflectionPackage');
         $refPackage = new stubReflectionPackage(stubClassLoader::getPackageName($this->getClassName()));
         return $refPackage;
    }

    /**
     * returns the full qualified class name
     *
     * @return  string
     * @XMLIgnore
     */
    public function getClassName()
    {
        return stubClassLoader::getFullQualifiedClassName(get_class($this));
    }

    /**
     * returns the name of the package where the class is inside
     *
     * @return  string
     * @XMLIgnore
     */
    public function getPackageName()
    {
        return stubClassLoader::getPackageName($this->getClassName());
    }

    /**
     * returns a unique hash code for the class
     *
     * @return  string
     * @XMLIgnore
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof stubObject) {
            return ($this->hashCode() == $compare->hashCode());
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * net::stubbles::lang::exceptions::stubRuntimeException {
     *     message(string): This is a runtime exception.
     *     file(string): foo.php
     *     line(integer): 4
     *     code(integer): 3
     *     stacktrace(string): __STACKTRACE__
     * }
     * [stack trace]
     * </code>
     *
     * @return  string
     * @XMLIgnore
     */
    public function __toString()
    {
        $string  = $this->getClassName() . " {\n";
        $string .= '    message(string): ' . $this->getMessage() . "\n";
        $string .= '    file(string): ' . $this->getFile() . "\n";
        $string .= '    line(integer): ' . $this->getLine() . "\n";
        $string .= '    code(integer): ' . $this->getCode() . "\n";
        $string .= '    stacktrace(string): ' . $this->getTraceAsString() . "\n";
        $string .= "}\n";
        return $string;
    }
}
?>