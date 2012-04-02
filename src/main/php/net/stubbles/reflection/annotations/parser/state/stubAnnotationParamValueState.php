<?php
/**
 * Parser is inside an annotation param value
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 * @version     $Id: stubAnnotationParamValueState.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAbstractState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationState'
);
/**
 * Parser is inside an annotation param value
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state
 */
class stubAnnotationParamValueState extends stubAnnotationAbstractState implements stubAnnotationState
{
    /**
     * character in which the value is enclosed
     *
     * @var  string
     */
    private $enclosed   = null;
    /**
     * whether the value is a string or not
     *
     * @var  bool
     */
    private $isString   = false;
    /**
     * the extracted value
     *
     * @var  string
     */
    private $value      = '';
    /**
     * switch whether the next token is escaped or not
     *
     * @var  bool
     */
    private $escapeNext = false;
    
    /**
     * returns the value
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * checks whether the value is a string or not
     *
     * @return  bool
     */
    public function isString()
    {
        return $this->isString;
    }
    
    /**
     * checks if the next token is escaped
     *
     * @return  bool
     */
    public function isNextCharacterEscaped()
    {
        return $this->escapeNext;
    }
    
    /**
     * returns the character in which the value is enclosed
     *
     * @return  string
     */
    public function getEnclosed()
    {
        return $this->enclosed;
    }

    /**
     * mark this state as the currently used state
     */
    public function selected()
    {
        parent::selected();
        $this->value      = '';
        $this->enclosed   = null;
        $this->isString   = false;
        $this->escapeNext = false;
    }

    /**
     * processes a token
     *
     * @param  string  $token
     */
    public function process($token)
    {
        if (true === $this->escapeNext) {
            $this->value     .= $token;
            $this->escapeNext = false;
            return;
        }
        
        if (null === $this->enclosed) {
            if ("'" === $token || '"' === $token) {
                if (strlen($this->value) > 0) {
                    $this->value .= $token;
                } else {
                    $this->enclosed = $token;
                    $this->isString = true;
                }
                
                return;
            }
            
            if (',' === $token) {
                $this->parser->setAnnotationParamValue($this->value, $this->isString);
                $this->parser->changeState(stubAnnotationState::PARAMS);
                return;
            }
            
            if (')' === $token) {
                $this->parser->setAnnotationParamValue($this->value, $this->isString);
                $this->parser->changeState(stubAnnotationState::DOCBLOCK);
                return;
            }
        } else {
            if ($this->enclosed === $token) {
                $this->enclosed = null;
                $this->parser->setAnnotationParamValue($this->value, $this->isString);
                $this->parser->changeState(stubAnnotationState::PARAMS);
                return;
            }
            
            if ('\\' === $token) {
                $this->escapeNext = true;
                return;
            }
        }
        
        $this->value .= $token;
    }
}
?>