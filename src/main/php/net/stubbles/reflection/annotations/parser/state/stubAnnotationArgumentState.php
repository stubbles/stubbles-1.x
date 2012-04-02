<?php
/**
 * Parser is inside the annotation argument.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationArgumentState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Parser is inside the annotation argument.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationArgumentState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * argument for which the annotation stands for
     *
     * @var  string
     */
    private $argument = '';

    /**
     * returns the type
     *
     * @return  string
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->argument = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  ReflectionException
     */
    public function process($token)
    {
        if ('}' === $token) {
            if (strlen($this->argument) > 0) {
                if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->argument) == false) {
                    throw new ReflectionException('Annotation argument may contain letters, underscores and numbers, but contains an invalid character.');
                }
                
                $this->parser->setAnnotationForArgument($this->argument);
            }
            
            $this->parser->changeState(stubAnnotationState::ANNOTATION);
            return;
        }

        $this->argument .= $token;
    }
}
?>