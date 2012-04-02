<?php
/**
 * Docblock state
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationDocblockState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Docblock state
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationDocblockState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  ReflectionException
     */
    public function process($token)
    {
        if ('@' === $token) {
            $this->parser->changeState(stubAnnotationState::ANNOTATION_NAME);
            return;
        }
        
        // all character except * and space and line breaks
        if (' ' !== $token && '*' !== $token && "\n" !== $token && "\t" !== $token) {
            $this->parser->changeState(stubAnnotationState::TEXT);
        }
    }
}
?>