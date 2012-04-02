<?php
/**
 * Class to read XML files and turn them into simple PHP types.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer
 * @version     $Id: stubXMLUnserializerOption.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Class to read XML files and turn them into simple PHP types.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer
 */
class stubXMLUnserializerOption extends stubBaseObject
{
    /**
     * option: name of the attribute that stores the type
     *
     * Possible values:
     * - any string
     */
    const ATTRIBUTE_KEY       = 'keyAttribute';
    /**
     * option: name of the attribute that stores the type
     *
     * Possible values:
     * - any string
     */
    const ATTRIBUTE_TYPE      = 'typeAttribute';
    /**
     * option: whether to parse attributes
     *
     * Possible values:
     * - true or false
     */
    const ATTRIBUTES_PARSE    = 'parseAttributes';
    /**
     * option: key of the array to store attributes (if any)
     *
     * Possible values:
     * - any string
     * - false (disabled)
     */
    const ATTRIBUTES_ARRAYKEY = 'attributesArray';
    /**
     * option: string to prepend attribute name (if any)
     *
     * Possible values:
     * - any string
     * - false (disabled)
     */
    const ATTRIBUTES_PREPEND  = 'prependAttributes';
    /**
     * option: key to store the content, if XML_UNSERIALIZER_ATTRIBUTES_PARSE is used
     *
     * Possible values:
     * - any string
     */
    const CONTENT_KEY         = 'contentName';
    /**
     * option: map tag names
     *
     * Possible values:
     * - associative array
     */
    const TAG_MAP             = 'tagMap';
    /**
     * option: list of tags that will always be enumerated
     *
     * Possible values:
     * - indexed array
     */
    const FORCE_LIST          = 'forceList';
    /**
     * option: encoding of the XML document
     *
     * Possible values:
     * - UTF-8
     * - ISO-8859-1
     */
    const ENCODING_SOURCE     = 'encoding';
    /**
     * option: list of tags, that will not be used as keys
     *
     * Possible values:
     * - true or false
     */
    const IGNORE_KEYS         = 'ignoreKeys';
    /**
     * option: whether to use type guessing for scalar values
     *
     * Possible values:
     * - true or false
     */
    const GUESS_TYPES         = 'guessTypes';
    /**
     * option: set the whitespace behaviour
     *
     * Possible values:
     * - WHITESPACE_KEEP
     * - WHITESPACE_TRIM
     * - WHITESPACE_NORMALIZE
     */
    const WHITESPACE          = 'whitespace';
    /**
     * Keep all whitespace
     */
    const WHITESPACE_KEEP     = 'keep';
    /**
     * remove whitespace from start and end of the data
     */
    const WHITESPACE_TRIM     = 'trim';
    /**
     * normalize whitespace
     */
    const WHITESPACE_NORMALIZE = 'normalize';
    /**
     * default options for the serialization
     *
     * @var  array<string,mixed>
     */
    protected static $defaultOptions = array(self::ATTRIBUTE_KEY       => '_originalKey',         // get array key/property name from this attribute
                                             self::ATTRIBUTE_TYPE      => '_type',                // get type from this attribute
                                             self::ATTRIBUTES_PARSE    => false,                  // parse the attributes of the tag into an array
                                             self::ATTRIBUTES_ARRAYKEY => false,                  // parse them into sperate array (specify name of array here)
                                             self::ATTRIBUTES_PREPEND  => '',                     // prepend attribute names with this string
                                             self::CONTENT_KEY         => '_content',             // put cdata found in a tag that has been converted to a complex type in this key
                                             self::TAG_MAP             => array(),                // use this to map tagnames
                                             self::FORCE_LIST          => array(),                // these tags will always be an indexed array
                                             self::ENCODING_SOURCE     => null,                   // specify the encoding character of the document to parse
                                             self::WHITESPACE          => self::WHITESPACE_TRIM,  // remove whitespace around data
                                             self::IGNORE_KEYS         => array(),                // list of tags that will automatically be added to the parent, instead of adding a new key
                                             self::GUESS_TYPES         => false                   // Whether to use type guessing
                                       );

    /**
     * returns list of default options
     *
     * @return  array<string,mixed>
     */
    public static function getDefault()
    {
        return self::$defaultOptions;
    }
}
?>