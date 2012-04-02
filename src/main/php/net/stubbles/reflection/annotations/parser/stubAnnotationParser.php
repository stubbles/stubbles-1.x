<?php
/**
 * Interface for parsers to parse Java-Style annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser
 * @version     $Id: stubAnnotationParser.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for parsers to parse Java-Style annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser
 */
interface stubAnnotationParser
{
    /**
     * change the current state
     *
     * @param  int     $state
     * @param  string  $token  token that should be processed by the state
     */
    public function changeState($state, $token = null);

    /**
     * parse a docblock and return all annotations found
     *
     * @param   string  $docBlock
     * @return  array
     */
    public function parse($docBlock);

    /**
     * register a new annotation
     *
     * @param  string  $name
     */
    public function registerAnnotation($name);

    /**
     * register a new annotation param
     *
     * @param  string  $name
     */
    public function registerAnnotationParam($name);

    /**
     * register single annotation param
     *
     * @param   string  $value     the value of the param
     * @param   bool    $asString  whether the value is a string or not
     * @throws  ReflectionException
     */
    public function registerSingleAnnotationParam($value, $asString = false);

    /**
     * set the annoation param value for the current annotation
     *
     * @param   string  $value     the value of the param
     * @param   bool    $asString  whether the value is a string or not
     * @throws  ReflectionException
     */
    public function setAnnotationParamValue($value, $asString = false);

    /**
     * set the type of the current annotation
     *
     * @param  string  $type  type of the annotation
     */
    public function setAnnotationType($type);

    /**
     * sets the argument for which the annotation is declared
     *
     * @param  string  $argument  name of the argument
     */
    public function setAnnotationForArgument($argument);
}
?>