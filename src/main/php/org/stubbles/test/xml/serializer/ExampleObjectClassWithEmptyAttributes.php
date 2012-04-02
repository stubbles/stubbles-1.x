<?php
/**
 * Simple example class to test the xml serializer with empty attribute values.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectClassWithEmptyAttributes.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with empty attribute values.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='test')
 */
class ExampleObjectClassWithEmptyAttributes
{
    /**
     * Empty property
     *
     * @var mixed
     * @XMLAttribute(attributeName='emptyProp')
     */
    public $emptyProp;
    /**
     * Empty property
     *
     * @var mixed
     * @XMLAttribute(attributeName='emptyProp2', skipEmpty=false)
     */
    public $emptyProp2;

    /**
     * Empty return value
     *
     * @return mixed
     * @XMLAttribute(attributeName='emptyMethod')
     */
    public function getEmpty() {
        return null;
    }

    /**
     * Empty return value
     *
     * @return mixed
     * @XMLAttribute(attributeName='emptyMethod2', skipEmpty=false)
     */
    public function getEmpty2() {
        return null;
    }
}
?>