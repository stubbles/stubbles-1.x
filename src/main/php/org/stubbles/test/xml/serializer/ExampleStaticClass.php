<?php
/**
 * Simple example class to test the xml serializer and static properties/methods.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleStaticClass.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer and static properties/methods.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 */
class ExampleStaticClass
{
    /**
     * static property
     *
     * @var  string
     */
    public static $foo = 'foo';

    /**
     * static method
     *
     * @return  string
     */
    public static function getBar()
    {
        return 'bar';
    }
}
?>