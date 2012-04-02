<?php
/**
 * Simple example class to test the xml serializer with xml fragments.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @version     $Id: ExampleObjectWithXmlFragments.php 2977 2011-02-07 18:55:46Z mikey $
 */
/**
 * Simple example class to test the xml serializer with xml fragments.
 *
 * @package     stubbles_test
 * @subpackage  xml_serializer
 * @XMLTag(tagName='test')
 */
class ExampleObjectWithXmlFragments
{
    /**
     * property containing XML
     *
     * @var string
     * @XMLFragment(tagName='xml');
     */
    public $xml = '<foo>bar</foo>';
    /**
     * another property containing XML
     *
     * @var string
     * @XMLFragment(tagName=false);
     */
    public $xml2 = '<foo>bar</foo>';

    /**
     * method returning xml
     *
     * @return  string
     * @XMLFragment(tagName='description', transformNewLineToBr=true);
     */
    public function getSomeXml()
    {
        return "foo\nb&ar\n\nbaz";
    }
}
?>