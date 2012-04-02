<?php
/**
 * Class to read XML files and turn them into simple PHP types.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer
 * @version     $Id: stubXMLUnserializer.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubFileNotFoundException',
                      'net::stubbles::xml::stubXMLException',
                      'net::stubbles::xml::unserializer::stubXMLUnserializerOption'
);
/**
 * Class to read XML files and turn them into simple PHP types.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer
 */
class stubXMLUnserializer extends stubBaseObject
{
    /**
     * current options for the serialization
     *
     * @var  array<string,mixed>
     */
    protected $options   = array();
    /**
     * current depth within the parsed document
     *
     * @var  int
     */
    protected $depth      = 0;
    /**
     * stack of opened elements while parsing
     *
     * @var  array
     */
    protected $dataStack  = array();
    /**
     * value stack
     *
     * @var  array
     */
    protected $valueStack = array();

    /**
     * constructor
     *
     * @param  array<string,mixed>  $options
     */
    public function __construct(array $options = null)
    {
        if (null === $options) {
            $this->options = stubXMLUnserializerOption::getDefault();
        } else {
            $this->options = array_merge(stubXMLUnserializerOption::getDefault(), $options);
        }
    }

    /**
     * unserializes data from given file
     *
     * @param   string  $fileName
     * @return  mixed
     * @throws  stubFileNotFoundException
     * @throws  stubXMLException
     */
    public function unserializeFile($fileName)
    {
        if (file_exists($fileName) === false) {
            throw new stubFileNotFoundException($fileName);
        }
        
        $reader = $this->initParser();
        if ($reader->open($fileName, $this->options[stubXMLUnserializerOption::ENCODING_SOURCE]) === false) {
            throw new stubXMLException('Failed to unserialize contents of ' . $fileName . ', can not create XML reader for given file.');
        }
        
        $result = $this->parse($reader);
        $reader->close($fileName);
        return $result;
    }

    /**
     * unserialize data
     *
     * @param   string  $data  data to unserialize
     * @return  mixed
     * @throws  stubXMLException
     */
    public function unserialize($data)
    {
        $reader = $this->initParser();
        if ($reader->xml($data, $this->options[stubXMLUnserializerOption::ENCODING_SOURCE]) === false) {
            throw new stubXMLException('Failed to unserialize data, can not create XML reader for given data.');
        }
        
        return $this->parse($reader);
    }

    /**
     * parses the xml document and creates the data structure from it
     *
     * @param   XMLReader $reader
     * @return  mixed
     */
    protected function parse($reader)
    {
        $data = null;
        while ($reader->read()) {
            switch ($reader->nodeType) {
                case XMLReader::ELEMENT:
                    $empty        = $reader->isEmptyElement;
                    $nameSpaceURI = $reader->namespaceURI;
                    $elementName  = $reader->localName;
                    $attributes   = array();
                    if (true == $reader->hasAttributes) {
                        // go to first attribute
                        $attribute = $reader->moveToFirstAttribute();
                        // save data of all attributes
                        while (true == $attribute) {
                            $attributes[$reader->localName] = $reader->value;
                            $attribute = $reader->moveToNextAttribute();
                        }
                    }

                    $this->startElement($nameSpaceURI, $elementName, $attributes);
                    if (true === $empty) {
                        $data = $this->endElement($nameSpaceURI, $elementName);
                    }
                    break;

                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $this->characters($reader->value);
                    break;

                case XMLReader::END_ELEMENT:
                    $data = $this->endElement($reader->namespaceURI, $reader->localName);
                    break;
                
                default:
                    // intentionally empty
            }
        }
        
        return $data;
    }

    /**
     * initializes the parser
     * 
     * @return  XMLReader $reader
     */
    protected function initParser()
    {
        $reader = new XMLReader();
        return $reader;
    }

    /**
     * handles the start element
     *
     * Creates a new Tag object and pushes it
     * onto the stack.
     *
     * @param  string  $namespaceURI  namespace of start tag
     * @param  string  $sName         name of start tag
     * @param  array   $atts          attributes of tag
     */
    protected function startElement($namespaceURI, $sName, $atts)
    {
        $this->depth++;
        $this->dataStack[$this->depth] = null;
        if (isset($atts[$this->options[stubXMLUnserializerOption::ATTRIBUTE_TYPE]])) {
            $type      = $atts[$this->options[stubXMLUnserializerOption::ATTRIBUTE_TYPE]];
            $guessType = false;
        } else {
            $type     = 'string';
            $guessType = $this->options[stubXMLUnserializerOption::GUESS_TYPES];
        }
        
        if (is_array($this->options[stubXMLUnserializerOption::TAG_MAP]) && isset($this->options[stubXMLUnserializerOption::TAG_MAP][$sName])) {
            $sName = $this->options[stubXMLUnserializerOption::TAG_MAP][$sName];
        }

        $val = array('name'         => $sName,
                     'value'        => null,
                     'type'         => $type,
                     'guessType'    => $guessType,
                     'childrenKeys' => array(),
                     'aggregKeys'   => array()
               );
        if (true === $this->options[stubXMLUnserializerOption::ATTRIBUTES_PARSE] && (count($atts) > 0)) {
            $val['children'] = array();
            $val['type']     = 'array';
            $val['class']    = $sName;

            if ($this->options[stubXMLUnserializerOption::GUESS_TYPES] === true) {
                $atts = $this->guessAndSetType($atts);
            }
            
            if ($this->options[stubXMLUnserializerOption::ATTRIBUTES_ARRAYKEY] != false) {
                $val['children'][$this->options[stubXMLUnserializerOption::ATTRIBUTES_ARRAYKEY]] = $atts;
            } else {
                foreach ($atts as $attrib => $value) {
                    $val['children'][$this->options[stubXMLUnserializerOption::ATTRIBUTES_PREPEND] . $attrib] = $value;
                }
            }
        }

        $keyAttr = $this->getKeyAttribute($sName);
        if (null !== $keyAttr && isset($atts[$keyAttr]) === true) {
            $val['name'] = $atts[$keyAttr];
        }

        array_push($this->valueStack, $val);
    }

    /**
     * helper method to detect the key attribute
     *
     * @param   string  $sName  name of element to retrieve key attribute for
     * @return  string
     */
    protected function getKeyAttribute($sName)
    {
        if (is_string($this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]) === true) {
            return $this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY];
        }
        
        if (is_array($this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]) === false) {
            return null;
        }
        
        
        if (isset($this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY][$sName]) === true) {
            return $this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY][$sName];
        }
        
        if (isset($this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]['#default']) === true) {
            return $this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]['#default'];
        }
        
        if (isset($this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]['__default']) === true) {
            return $this->options[stubXMLUnserializerOption::ATTRIBUTE_KEY]['__default'];
        }
        
        return null;
    }

    /**
     * handles the end element
     *
     * Fetches the current element from the stack and
     * converts it to the correct type.
     *
     * @param   string  $namespaceURI  namespace of end tag
     * @param   string  $sName         name of end tag
     * @return  mixed
     */
    protected function endElement($namespaceURI, $sName)
    {
        $value = array_pop($this->valueStack);
        switch ($this->options[stubXMLUnserializerOption::WHITESPACE]) {
            case stubXMLUnserializerOption::WHITESPACE_KEEP:
                $data = $this->dataStack[$this->depth];
                break;
            
            case stubXMLUnserializerOption::WHITESPACE_NORMALIZE:
                $data = trim(preg_replace('/\s\s+/m', ' ', $this->dataStack[$this->depth]));
                break;
            
            case stubXMLUnserializerOption::WHITESPACE_TRIM:
            default:
                $data = trim($this->dataStack[$this->depth]);
                break;
        }

        // adjust type of the value
        switch (strtolower($value['type'])) {
            // unserialize an object
            case 'object':
                $value['value'] = new stdClass();
                if (trim($data) !== '') {
                    if (true === $value['guessType']) {
                        $data = $this->guessAndSetType($data);
                    }
                    
                    $value['children'][$this->options[stubXMLUnserializerOption::CONTENT_KEY]] = $data;
                }

                // set properties
                foreach ($value['children'] as $prop => $propVal) {
                    $value['value']->$prop = $propVal;
                }
                break;

            // unserialize an array
            case 'array':
                if (trim($data) !== '') {
                    if (true === $value['guessType']) {
                        $data = $this->guessAndSetType($data);
                    }
                    
                    $value['children'][$this->options[stubXMLUnserializerOption::CONTENT_KEY]] = $data;
                }
                
                if (isset($value['children']) === true) {
                    $value['value'] = $value['children'];
                } else {
                    $value['value'] = array();
                }
                break;

            // unserialize a null value
            case 'null':
                $data = null;
                break;

            // unserialize a resource => this is not possible :-(
            case 'resource':
                $value['value'] = $data;
                break;

            // unserialize any scalar value
            default:
                if (true === $value['guessType']) {
                    $data = $this->guessAndSetType($data);
                } else {
                    settype($data, $value['type']);
                }
            
                $value['value'] = $data;
                break;
        }
        
        $parent = array_pop($this->valueStack);
        if (null === $parent) {
            return $value['value'];
        } else {
            // parent has to be an array
            if (isset($parent['children']) === false || is_array($parent['children']) === false) {
                $parent['children'] = array();
                if ('array' !== $parent['type']) {
                    $parent['type'] = 'array';
                }
            }

            $ignoreKey = in_array($sName, $this->options[stubXMLUnserializerOption::IGNORE_KEYS]);
            if (empty($value['name']) === false && false === $ignoreKey) {
                // there already has been a tag with this name
                if (in_array($value['name'], $parent['childrenKeys']) === true || in_array($value['name'], $this->options[stubXMLUnserializerOption::FORCE_LIST]) === true) {
                    // no aggregate has been created for this tag
                    if (in_array($value['name'], $parent['aggregKeys']) === false) {
                        if (isset($parent['children'][$value['name']]) === true) {
                            $parent['children'][$value['name']] = array($parent['children'][$value['name']]);
                        } else {
                            $parent['children'][$value['name']] = array();
                        }
                        
                        array_push($parent['aggregKeys'], $value['name']);
                    }
                    array_push($parent['children'][$value['name']], $value['value']);
                } else {
                    $parent['children'][$value['name']] = &$value['value'];
                    array_push($parent['childrenKeys'], $value['name']);
                }
            } else {
                array_push($parent['children'], $value['value']);
            }
            
            array_push($this->valueStack, $parent);
        }
        
        $this->depth--;
    }

    /**
     * character data handler
     *
     * Fetches the current tag from the stack and
     * appends the data.
     *
     * @param  string  $buf
     */
    protected function characters($buf)
    {
        $this->dataStack[$this->depth] .= $buf;
    }

    /**
     * try to guess the type of a value and set it accordingly
     *
     * @param   string  $value  character data
     * @return  mixed           value with the best matching type
     */
    protected function guessAndSetType($value)
    {
        if (is_array($value) === true) {
            return array_map(array($this, 'guessAndSetType'), $value);
        }
        
        if ('true' === $value) {
            return true;
        }
        
        if ('false' === $value) {
            return false;
        }
        
        if ('NULL' === $value) {
            return null;
        }
        
        if (preg_match('/^[-+]?[0-9]{1,}$/', $value) != false) {
            return intval($value);
        }
        
        if (preg_match('/^[-+]?[0-9]{1,}\.[0-9]{1,}$/', $value) != false) {
            return doubleval($value);
        }
        
        return (string) $value;
    }
}
?>