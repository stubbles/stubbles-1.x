<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslAbstractCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslAbstractCallbackTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslAbstractCallback',
                      'net::stubbles::xml::stubDomXMLStreamWriter'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 */
class TeststubXslAbstractCallback extends stubXslAbstractCallback
{
    /**
     * access to protected method
     *
     * @param   array<DOMAttr>|string  $value
     * @return  string
     */
    public function parseValue($value)
    {
        return parent::parseValue($value);
    }
}
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslAbstractCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslAbstractCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubXslAbstractCallback
     */
    protected $xslAbstractCallback;
    /**
     * instance to test
     *
     * @var  stubDomXMLStreamWriter
     */
    protected $mockXMLStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXMLStreamWriter = new stubDomXMLStreamWriter();
        $this->xslAbstractCallback = new TeststubXslAbstractCallback($this->mockXMLStreamWriter);
    }

    /**
     * make sure annotations are present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->xslAbstractCallback
                               ->getClass()
                               ->getConstructor()
                               ->hasAnnotation('Inject')
        );
    }

    /**
     * parse a non-array value
     *
     * @test
     */
    public function parseNonArrayValue()
    {
        $this->assertEquals('foo', $this->xslAbstractCallback->parseValue('foo'));
    }

    /**
     * parse an empty array value
     *
     * @test
     */
    public function parseEmptyArrayValue()
    {
        $this->assertEquals('', $this->xslAbstractCallback->parseValue(array()));
    }

    /**
     * parse a non-empty array value with no element at position 0
     *
     * @test
     */
    public function parseNonEmptyArrayValueWithNoElementAtPosition0()
    {
        $foo        = new DOMAttr('bar');
        $foo->value = 'foo';
        $this->assertEquals('', $this->xslAbstractCallback->parseValue(array(1 => $foo)));
    }

    /**
     * parse non-empty array value
     *
     * @test
     */
    public function parseNonEmptyArrayValue()
    {
        $foo        = new DOMAttr('bar');
        $foo->value = 'foo';
        $this->assertEquals('foo', $this->xslAbstractCallback->parseValue(array($foo)));
    }

    /**
     * parse non-empty array value which is not a DOMAttr instance
     *
     * @test
     */
    public function parseNonEmptyArrayValueWithNoDomAttrInstance()
    {
        $this->assertEquals('', $this->xslAbstractCallback->parseValue(array(new stdClass())));
    }
}
?>