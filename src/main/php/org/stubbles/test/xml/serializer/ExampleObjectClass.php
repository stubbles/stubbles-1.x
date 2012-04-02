<?php
/**
 * Simple example class to test the xml serializer.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectClass.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='foo')
 */
class ExampleObjectClass
{
    /**
     * Scalar property
     *
     * @var int
     * @XMLTag(tagName='bar')
     */
    public $bar = 42;
    /**
     * Another scalar property
     *
     * @var string
     * @XMLAttribute(attributeName='bar')
     */
    public $scalar = "test";
    /**
     * Should not be exported to XML
     *
     * @var string
     * @XMLIgnore
     */
    public $ignoreMe = 'Ignore';
}
?>