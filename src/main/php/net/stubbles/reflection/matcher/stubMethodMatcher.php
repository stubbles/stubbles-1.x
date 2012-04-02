<?php
/**
 * Interface for matching methods.
 * 
 * @package     stubbles
 * @subpackage  reflection_matcher
 * @version     $Id: stubMethodMatcher.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for matching methods.
 * 
 * @package     stubbles
 * @subpackage  reflection_matcher
 */
interface stubMethodMatcher extends stubObject
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(ReflectionMethod $method);

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   stubReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(stubReflectionMethod $method);
}
?>