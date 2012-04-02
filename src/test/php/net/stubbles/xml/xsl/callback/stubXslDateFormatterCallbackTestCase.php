<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslDateFormatterCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslDateFormatterCallbackTestCase.php 2972 2011-02-07 18:32:07Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslDateFormatterCallback',
                      'net::stubbles::xml::stubDomXMLStreamWriter'
);
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslDateFormatterCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslDateFormatterCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXslDateFormatterCallback
     */
    protected $xslDateFormatterCallback;
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
        $this->mockXMLStreamWriter      = new stubDomXMLStreamWriter();
        $this->xslDateFormatterCallback = new stubXslDateFormatterCallback($this->mockXMLStreamWriter);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->xslDateFormatterCallback
                               ->getClass()
                               ->getConstructor()
                               ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function formatDateMethodIsAnnotatedAsXslMethod()
    {
        $this->assertTrue($this->xslDateFormatterCallback
                               ->getClass()
                               ->getMethod('formatDate')
                               ->hasAnnotation('XslMethod')
        );
    }

    /**
     * @test
     */
    public function formatLocaleDateMethodIsAnnotatedAsXslMethod()
    {
        $this->assertTrue($this->xslDateFormatterCallback
                               ->getClass()
                               ->getMethod('formatLocaleDate')
                               ->hasAnnotation('XslMethod')
        );
    }

    /**
     * @test
     */
    public function formatDateUsesGivenTimestamp()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>'
                              . "\n<date timestamp=\"1216222717\">2008-07-16</date>\n",
                            $this->xslDateFormatterCallback->formatDate('Y-m-d',
                                                                        '1216222717'
                                                             )
                                                           ->saveXML()
        );
    }

    /**
     * @test
     */
    public function formatDateWithoutTimestampUsesCurrentTimestamp()
    {
        $dateNode = $this->xslDateFormatterCallback->formatDate('Y-m-d')
                                                   ->getElementsByTagName('date')
                                                   ->item(0);
        $this->assertLessThanOrEqual(time(),
                                     $dateNode->attributes->getNamedItem('timestamp')
                                                          ->textContent
        );
        $this->assertEquals(date('Y-m-d'), $dateNode->textContent);
    }

    /**
     * @test
     */
    public function formatLocaleDateUsesGivenTimestamp()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>'
                              . "\n<date timestamp=\"1216222717\">16 Jul 2008</date>\n",
                            $this->xslDateFormatterCallback->formatLocaleDate('%d %b %Y',
                                                                              '1216222717'
                                                             )
                                                           ->saveXML()
        );
    }

    /**
     * @test
     */
    public function formatLocaleDateWithoutTimestampUsesCurrentTimestamp()
    {
        $dateNode = $this->xslDateFormatterCallback->formatLocaleDate('%d %b %Y')
                                                   ->getElementsByTagName('date')
                                                   ->item(0);
        $this->assertLessThanOrEqual(time(),
                                     $dateNode->attributes->getNamedItem('timestamp')
                                                          ->textContent
        );
        $this->assertEquals(strftime('%d %b %Y'),
                            $dateNode->textContent
        );
    }
}
?>