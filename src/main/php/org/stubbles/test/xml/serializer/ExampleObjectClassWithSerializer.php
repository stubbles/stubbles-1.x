<?php
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectClassWithSerializer.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with an annotated serializer class.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XmlSerializer(org::stubbles::test::xml::serializer::ExampleObjectSerializer.class)
 */
class ExampleObjectClassWithSerializer
{
    /**
     * a property
     *
     * @var  int
     */
    public $bar    = 303;
    /**
     * another property
     *
     * @var  string
     */
    public $scalar = 'not interesting';

    /**
     * returns something
     *
     * @return  string
     */
    public function getSomething()
    {
        return 'something';
    }
}
?>