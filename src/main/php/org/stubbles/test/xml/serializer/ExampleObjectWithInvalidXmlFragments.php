<?php
/**
 * Simple example class to test the xml serializer with invalid xml fragments.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectWithInvalidXmlFragments.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with invalid xml fragments.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='test')
 */
class ExampleObjectWithInvalidXmlFragments
{
    /**
     * property containing no XML
     *
     * @var string
     * @XMLFragment(tagName='noXml');
     */
    public $noXml = 'bar';
    /**
     * another property containing no data
     *
     * @var string
     * @XMLFragment(tagName='noData');
     */
    public $noData;

    /**
     * method returnin no valid xml
     *
     * @return  string
     * @XMLFragment(tagName=false);
     */
    public function noXml()
    {
        return '';
    }
}
?>