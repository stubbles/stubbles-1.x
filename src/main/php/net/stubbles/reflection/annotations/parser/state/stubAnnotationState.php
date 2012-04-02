<?php
/**
 * Interface for an annotation parser state
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::stubAnnotationParser');
/**
 * Interface for an annotation parser state
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
interface stubAnnotationState
{
    /**
     * parser is inside the standard docblock
     */
    const DOCBLOCK        = 0;
    /**
     * parser is inside a text within the docblock
     */
    const TEXT            = 1;
    /**
     * parser is inside an annotation
     */
    const ANNOTATION      = 2;
    /**
     * parser is inside an annotation name
     */
    const ANNOTATION_NAME = 3;
    /**
     * parser is inside an annotation type
     */
    const ANNOTATION_TYPE = 4;
    /**
     * parser is inside the annotation params
     */
    const PARAMS          = 5;
    /**
     * parser is inside an annotation param name
     */
    const PARAM_NAME      = 6;
    /**
     * parser is inside an annotation param value
     */
    const PARAM_VALUE     = 7;
    /**
     * parser is inside a argument declaration
     */
    const ARGUMENT        = 8;

    /**
     * constructor
     *
     * @param  stubAnnotationParser  $parser
     */
    public function __construct(stubAnnotationParser $parser);

    /**
     * mark this state as the currently used state
     */
    public function selected();

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  ReflectionException
     */
    public function process($token);
}
?>