<?php
/**
 * Parser to parse Java-Style annotations.
 *
 * @author      Stephan Schmidt <schst@stubbles.net>
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  reflection_annotations_parser
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::reflection::annotations::parser::stubAnnotationParser',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationArgumentState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationDocblockState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationAnnotationState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationNameState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationParamNameState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationParamsState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationParamValueState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationTextState',
                      'net::stubbles::reflection::annotations::parser::state::stubAnnotationTypeState'
);
/**
 * Parser to parse Java-Style annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser
 */
class stubAnnotationStateParser extends stubBaseObject implements stubAnnotationParser
{
    /**
     * possible states
     *
     * @var  array
     */
    private $states             = array();
    /**
     * the current state
     *
     * @var  stubAnnotationParserState
     */
    private $currentState       = null;
    /**
     * the name of the current annotation
     *
     * @var  string
     */
    private $currentAnnotation  = null;
    /**
     * the name of the current annotation parameter
     *
     * @var  string
     */
    private $currentParam       = null;
    /**
     * all parsed annotations
     *
     * @var  array
     */
    private $annotations        = array();

    /**
     * constructor
     */
    public function __construct()
    {
        $this->states[stubAnnotationState::DOCBLOCK]        = new stubAnnotationDocblockState($this);
        $this->states[stubAnnotationState::TEXT]            = new stubAnnotationTextState($this);
        $this->states[stubAnnotationState::ANNOTATION]      = new stubAnnotationAnnotationState($this);
        $this->states[stubAnnotationState::ANNOTATION_NAME] = new stubAnnotationNameState($this);
        $this->states[stubAnnotationState::ANNOTATION_TYPE] = new stubAnnotationTypeState($this);
        $this->states[stubAnnotationState::ARGUMENT]        = new stubAnnotationArgumentState($this);
        $this->states[stubAnnotationState::PARAMS]          = new stubAnnotationParamsState($this);
        $this->states[stubAnnotationState::PARAM_NAME]      = new stubAnnotationParamNameState($this);
        $this->states[stubAnnotationState::PARAM_VALUE]     = new stubAnnotationParamValueState($this);
    }

    /**
     * change the current state
     *
     * @param   int     $state
     * @param   string  $token  token that should be processed by the state
     * @throws  ReflectionException
     */
    public function changeState($state, $token = null)
    {
        if (isset($this->states[$state]) == false) {
            throw new ReflectionException('Unknown state ' . $state);
        }

        $this->currentState = $this->states[$state];
        $this->currentState->selected();
        if (null != $token) {
            $this->currentState->process($token);
        }
    }

    /**
     * parse a docblock and return all annotations found
     *
     * @param   string  $docBlock
     * @return  array
     * @throws  ReflectionException
     */
    public function parse($docBlock)
    {
        $this->annotations = null;
        $this->changeState(stubAnnotationState::DOCBLOCK);
        $len = strlen($docBlock);
        for ($i = 0; $i < $len; $i++) {
            $this->currentState->process($docBlock{$i});
        }

        if (($this->currentState instanceof stubAnnotationDocblockState) == false
          && ($this->currentState instanceof stubAnnotationTextState) == false) {
            throw new ReflectionException('Annotation parser finished in wrong state, last annotation probably closed incorrectly, last state was ' . $this->currentState->getClassName());
        }

        return $this->annotations;
    }

    /**
     * register a new annotation
     *
     * @param  string  $name
     */
    public function registerAnnotation($name)
    {
        $this->annotations[$name] = array('type'     => $name,
                                          'params'   => array(),
                                          'argument' => null
                                    );
        $this->currentAnnotation  = $name;
    }

    /**
     * register a new annotation param
     *
     * @param  string  $name
     */
    public function registerAnnotationParam($name)
    {
        $this->currentParam = trim($name);
    }

    /**
     * register single annotation param
     *
     * @param   string  $value     the value of the param
     * @param   bool    $asString  whether the value is a string or not
     * @throws  ReflectionException
     */
    public function registerSingleAnnotationParam($value, $asString = false)
    {
        $value = $this->convertAnnotationValue($value, $asString);
        if (count($this->annotations[$this->currentAnnotation]['params']) > 0) {
            throw new ReflectionException('Error parsing annotation ' . $this->currentAnnotation);
        }

        $this->annotations[$this->currentAnnotation]['params']['value'] = $value;
    }

    /**
     * set the annoation param value for the current annotation
     *
     * @param  string  $value     the value of the param
     * @param  bool    $asString  whether the value is a string or not
     */
    public function setAnnotationParamValue($value, $asString = false)
    {
        $this->annotations[$this->currentAnnotation]['params'][$this->currentParam] = $this->convertAnnotationValue($value, $asString);
    }

    /**
     * set the type of the current annotation
     *
     * @param  string  $type  type of the annotation
     */
    public function setAnnotationType($type)
    {
        $this->annotations[$this->currentAnnotation]['type'] = $type;
    }

    /**
     * sets the argument for which the annotation is declared
     *
     * @param  string  $argument  name of the argument
     */
    public function setAnnotationForArgument($argument)
    {
        $this->annotations[$this->currentAnnotation . '#' . $argument] = $this->annotations[$this->currentAnnotation];
        unset($this->annotations[$this->currentAnnotation]);
        $this->currentAnnotation .= '#' . $argument;
        $this->annotations[$this->currentAnnotation]['argument'] = $argument;
    }

    /**
     * convert an annotation value
     *
     * @param   string   $value     the value to convert
     * @param   boolean  $asString  whether value should be treated as string or not
     * @return  mixed
     */
    protected function convertAnnotationValue($value, $asString)
    {
        if (true == $asString) {
            return (string) $value;
        }

        if ('true' === $value) {
            return true;
        }

        if ('false' === $value) {
            return false;
        }

        if ('null' === strtolower($value)) {
            return null;
        }

        if (preg_match('/^[+-]?[0-9]+$/', $value) != false) {
            return (integer) $value;
        }

        if (preg_match('/^[+-]?[0-9]+\.[0-9]+$/', $value) != false) {
            return (double) $value;
        }

        $matches = array();
        if (preg_match('/^([a-zA-Z_]{1}[a-zA-Z0-9_:]*)\.class/', $value, $matches) != false) {
            return new stubReflectionClass($matches[1]);
        }

        $matches = array();
        if (preg_match('/^([a-zA-Z_]{1}[a-zA-Z0-9_:]*)::\$([a-zA-Z_]{1}[a-zA-Z0-9_]*)/', $value, $matches) != false) {
            stubClassLoader::load('net::stubbles::lang::stubEnum');
            try {
                return stubEnum::forName(new stubReflectionClass($matches[1]), $matches[2]);
            } catch (Exception $e) {
                echo $e->getMessage();
                return null;
            }
        }

        if (defined($value) == true) {
            return constant($value);
        }

        return (string) $value;
    }
}
?>