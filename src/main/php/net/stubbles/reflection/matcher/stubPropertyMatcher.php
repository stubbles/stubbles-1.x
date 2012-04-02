<?php
/**
 * Interface for matching properties.
 * 
 * @package     stubbles
 * @subpackage  reflection_matcher
 * @version     $Id: stubPropertyMatcher.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for matching properties.
 * 
 * @package     stubbles
 * @subpackage  reflection_matcher
 */
interface stubPropertyMatcher extends stubObject
{
    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   ReflectionProperty  $property
     * @return  bool
     */
    public function matchesProperty(ReflectionProperty $property);

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   stubReflectionProperty  $property
     * @return  bool
     */
    public function matchesAnnotatableProperty(stubReflectionProperty $property);
}
?>