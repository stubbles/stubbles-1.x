<?php
/**
 * Base interface for all stubbles classes except static ones and classes
 * extending php built-in classes.
 * 
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubObject.php 2854 2011-01-10 13:25:44Z mikey $
 */
/**
 * Base interface for all stubbles classes except static ones and classes
 * extending php built-in classes.
 * 
 * @package     stubbles
 * @subpackage  lang
 */
interface stubObject
{
    /**
     * returns class informations
     *
     * @return  stubReflectionObject
     */
    public function getClass();

    /**
     * returns package informations
     *
     * @return  stubReflectionPackage
     */
    public function getPackage();

    /**
     * returns the full qualified class name
     *
     * @return  string
     */
    public function getClassName();

    /**
     * returns the name of the package where the class is inside
     *
     * @return  string
     */
    public function getPackageName();

    /**
     * returns a unique hash code for the class
     *
     * @return  string
     */
    public function hashCode();

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
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example.MyClass {
     *     foo(string): hello
     *     bar(example::AnotherClass): example::AnotherClass {
     *         baz(int): 5
     *     }
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString();
}
?>