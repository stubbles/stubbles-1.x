<?php
/**
 * Parser is inside the annotation name
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationNameState.php 2140 2009-03-20 14:50:50Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Parser is inside the annotation name
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationNameState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * list of forbidden annotation names
     *
     * @var  array<string>
     */
    protected $forbiddenAnnotationNames = array('deprecated',
                                                'example',
                                                'ignore',
                                                'internal',
                                                'link',
                                                'method',
                                                'package',
                                                'param',
                                                'property',
                                                'property-read',
                                                'property-write',
                                                'return',
                                                'see',
                                                'since',
                                                'static',
                                                'subpackage',
                                                'throws',
                                                'todo',
                                                'uses',
                                                'var',
                                                'version'
                                          );
    /**
     * name of the annotation
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
        if (' ' === $token) {
            if (strlen($this->name) == 0) {
                $this->changeState(stubAnnotationState::DOCBLOCK);
                return;
            }
            
            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(stubAnnotationState::ANNOTATION);
            return;
        }
        
        if ("\n" === $token || "\r" === $token) {
            if (strlen($this->name) > 0) {
                $this->checkName();
                $this->parser->registerAnnotation($this->name);
            }
            
            $this->changeState(stubAnnotationState::DOCBLOCK);
            return;
        }
        
        if ('{' === $token) {
            if (strlen($this->name) == 0) {
                throw new ReflectionException('Annotation name can not be empty');
            }
            
            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(stubAnnotationState::ARGUMENT);
            return;
        }
        
        if ('[' === $token) {
            if (strlen($this->name) == 0) {
                throw new ReflectionException('Annotation name can not be empty');
            }
            
            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(stubAnnotationState::ANNOTATION_TYPE);
            return;
        }

        if ('(' === $token) {
            if (strlen($this->name) == 0) {
                throw new ReflectionException('Annotation name can not be empty');
            }
            
            $this->checkName();
            $this->parser->registerAnnotation($this->name);
            $this->changeState(stubAnnotationState::PARAMS);
            return;
        }
        
        $this->name .= $token;
    }

    /**
     * check if the name is valid
     *
     * @throws  ReflectionException
     */
    protected function checkName()
    {
        if (preg_match('/^[a-zA-Z_]{1}[a-zA-Z_0-9]*$/', $this->name) == false) {
            throw new ReflectionException('Annotation parameter name may contain letters, underscores and numbers, but contains an invalid character.');
        }
    }
    
    /**
     * helper method to change state to another parsing state only if annotation
     * name is not forbidden, if it is forbidden change back to docblock state
     *
     * @param  int  $targetState  original target state
     */
    protected function changeState($targetState)
    {
        if (in_array($this->name, $this->forbiddenAnnotationNames) === true) {
            $targetState = stubAnnotationState::DOCBLOCK;
        }
        
        $this->parser->changeState($targetState);
    }
}
?>