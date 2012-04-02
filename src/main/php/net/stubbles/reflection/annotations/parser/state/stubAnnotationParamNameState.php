<?php
/**
 * Parser is inside an annotation param name
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationParamNameState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Parser is inside an annotation param name
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationParamNameState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * name of the param
     *
     * @var  string
     */
    private $name = '';

    /**
     * returns the name of the annotation
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->name = '';
    }

    /**
     * processes a token
     *
     * @param   string  $token
     * @throws  ReflectionException
     */
    public function process($token)
    {
        if ("'" === $token || '"' === $token) {
            if (strlen($this->name) > 0) {
                throw new ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains ' . $token . '. Probably an equal sign is missing.');
            }
            
            $this->parser->registerAnnotationParam('value');
            $this->parser->changeState(stubAnnotationState::PARAM_VALUE, $token);
            return;
        }
        
        if ('=' === $token) {
            if (strlen($this->name) == 0) {
                throw new ReflectionException('Annotation parameter name has to start with a letter or underscore, but starts with =');
            } elseif (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->name) == false) {
                throw new ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character.');
            }
            
            $this->parser->registerAnnotationParam($this->name);
            $this->parser->changeState(stubAnnotationState::PARAM_VALUE);
            return;
        }
        
        if (')' === $token) {
            if (strlen($this->name) > 0) {
                $this->parser->registerSingleAnnotationParam($this->name, false);
            }
            
            $this->parser->changeState(stubAnnotationState::DOCBLOCK);
            return;
        }
            
        $this->name .= $token;
    }
}
?>