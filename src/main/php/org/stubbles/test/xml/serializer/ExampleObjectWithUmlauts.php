<?php
/**
 * Simple example class to the xml serializer with german umlaut properties.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectWithUmlauts.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to the xml serializer with german umlaut properties.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='test')
 */
class ExampleObjectWithUmlauts
{
    /**
     * test property
     *
     * @var string
     * @XMLTag(tagName='foo')
     */
    public $foo = 'Hähnchen';
    /**
     * test attribute property
     *
     * @var string
     * @XMLAttribute(attributeName='bar')
     */
    public $ba = 'Hähnchen';
}
?>