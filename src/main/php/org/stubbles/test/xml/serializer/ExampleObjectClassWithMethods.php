<?php
/**
 * Simple example class to test the xml serializer.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectClassWithMethods.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with serialization of methods.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='class')
 */
class ExampleObjectClassWithMethods
{
    /**
     * constructor
     */
    public function __construct()
    {
        // intentionally empty
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        // intentionally empty
    }

    /**
     * another magic method
     *
     * @param  string  $prop
     */
    public function __get($prop)
    {
        // intentionally empty
    }

    /**
     * Return a value
     *
     * @return string
     * @XMLAttribute(attributeName='method')
     */
    public function getValue() {
        return "returned";
    }

    /**
     * return a boolean value
     *
     * @return  bool
     * @XMLAttribute(attributeName='isFoo')
     */
    public function isFoo()
    {
        return true;
    }

    /**
     * return a boolean value
     *
     * @return  bool
     * @XMLAttribute(attributeName='isBar')
     */
    public function isBar()
    {
        return false;
    }
}
?>