<?php
/**
 * Method matcher to match only public methods annotated with @RestMethod.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @version     $Id: stubRestMethodsMatcher.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::matcher::stubMethodMatcher');
/**
 * Method matcher to match only public methods annotated with @RestMethod.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @since       1.1.0
 */
class stubRestMethodsMatcher extends stubBaseObject implements stubMethodMatcher
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(ReflectionMethod $method)
    {
        return $method->isPublic();
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   stubReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(stubReflectionMethod $method)
    {
        return $method->hasAnnotation('RestMethod');
    }
}
?>