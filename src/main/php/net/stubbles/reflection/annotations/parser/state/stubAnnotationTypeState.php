<?php
/**
 * Parser is inside the annotation type
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationTypeState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Parser is inside the annotation type
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationTypeState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * type of the annotation
     *
     * @var  string
     */
    private $type = '';

    /**
     * returns the type
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->type = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  ReflectionException
     */
    public function process($token)
    {
        if (']' === $token) {
            if (strlen($this->type) > 0) {
                if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->type) == false) {
                    throw new ReflectionException('Annotation type may contain letters, underscores and numbers, but contains an invalid character.');
                }
                
                $this->parser->setAnnotationType($this->type);
            }
            
            $this->parser->changeState(stubAnnotationState::ANNOTATION);
            return;
        }

        $this->type .= $token;
    }
}
?>