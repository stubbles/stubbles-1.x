<?php
/**
 * Simple example class to test the xml serializer with object and array serialization.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ContainerWithArrayListWithoutTagName.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with object and array serialization.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='container')
 */
class ContainerWithArrayListWithoutTagName
{
    /**
     * array property
     *
     * @var  array
     * @XMLTag(tagName=false, elementTagName='item')
     */
    public $bar = array('one', 'two', 'three');
}
?>