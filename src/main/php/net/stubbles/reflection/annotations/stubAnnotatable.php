<?php
/**
 * Interface for reflected structures that may have annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @version     $Id: stubAnnotatable.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for reflected structures that may have annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 */
interface stubAnnotatable
{
    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName);

    /**
     * return the specified annotation
     *
     * @param   string          $annotationName
     * @return  stubAnnotation
     * @throws  ReflectionException
     */
    public function getAnnotation($annotationName);
}
?>