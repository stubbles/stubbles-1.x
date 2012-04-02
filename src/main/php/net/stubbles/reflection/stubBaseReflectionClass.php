<?php
/**
 * Marker interface for stubReflectionClass and stubReflectionObject.
 * 
 * @package     stubbles
 * @subpackage  reflection
 * @version     $Id: stubBaseReflectionClass.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotatable',
                      'net::stubbles::reflection::stubReflectionType',
                      'net::stubbles::reflection::matcher::stubMethodMatcher',
                      'net::stubbles::reflection::matcher::stubPropertyMatcher'
);
/**
 * Marker interface for stubReflectionClass and stubReflectionObject.
 * 
 * This interface allows to use
 * net::stubbles::reflection::stubReflectionClass and
 * net::stubbles::reflection::stubReflectionObject on
 * the same argument when the argument is typehinted with this interface.
 * 
 * @package     stubbles
 * @subpackage  reflection
 */
interface stubBaseReflectionClass extends stubReflectionType, stubAnnotatable
{
    /**
     * returns the full qualified class name of the reflected class
     * 
     * If the class has not been loaded with stubClassLoader the non qualified
     * class name will be returned.
     *
     * @return  string
     */
    public function getFullQualifiedClassName();

    /**
     * returns the constructor or null if none exists
     * 
     * Warning: PHP4-style constructors are not supported! If you have one use
     * getMethod($className) instead to retrieve the constructor reflection method.
     *
     * @return  stubReflectionMethod
     */
    public function getConstructor();

    /**
     * returns the specified method or null if it does not exist
     *
     * @param   string                $name  name of method to return
     * @return  stubReflectionMethod
     */
    public function getMethod($name);

    /**
     * returns a list of all methods
     *
     * @return  array<stubReflectionMethod>
     */
    public function getMethods();

    /**
     * returns a list of all methods which satify the given matcher
     *
     * @param   stubMethodMatcher            $methodMatcher
     * @return  array<stubReflectionMethod>
     */
    public function getMethodsByMatcher(stubMethodMatcher $methodMatcher);

    /**
     * returns the specified property or null if it does not exist
     *
     * @param   string                  $name  name of property to return
     * @return  stubReflectionProperty
     */
    public function getProperty($name);

    /**
     * returns a list of all properties
     *
     * @return  array<stubReflectionProperty>
     */
    public function getProperties();

    /**
     * returns a list of all properties which satify the given matcher
     *
     * @param   stubPropertyMatcher            $propertyMatcher
     * @return  array<stubReflectionProperty>
     */
    public function getPropertiesByMatcher(stubPropertyMatcher $propertyMatcher);

    /**
     * returns a list of all interfaces
     *
     * @return  array<stubReflectionClass>
     */
    public function getInterfaces();

    /**
     * returns a list of all interfaces
     *
     * @return  stubReflectionClass
     */
    public function getParentClass();

    /**
     * returns the extension to where this class belongs too
     *
     * @return  stubReflectionExtension
     */
    public function getExtension();

    /**
     * returns the package where the class belongs to
     *
     * @return  stubReflectionPackage
     */
    public function getPackage();

    /**
     * checks whether class implements a certain interface
     *
     * @param   string  $interface
     * @return  bool
     */
    public function implementsInterface($interface);
}
?>