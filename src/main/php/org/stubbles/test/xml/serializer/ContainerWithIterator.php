<?php
/**
 * Simple example class to test the xml serializer with object and iterator serialization.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ContainerWithIterator.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with object and iterator serialization.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='container')
 */
class ContainerWithIterator
{
    /**
     * array property
     *
     * @var  ArrayIterator
     * @XMLTag(tagName=false, elementTagName='item')
     */
    public $bar;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->bar = new ArrayIterator(array('one', 'two', 'three'));
    }
}
?>