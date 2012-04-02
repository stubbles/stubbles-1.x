<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslImageDimensionsCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslImageDimensionsCallbackTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslImageDimensionsCallback',
                      'net::stubbles::xml::stubDomXMLStreamWriter'
);
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslImageDimensionsCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslImageDimensionsCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXslImageDimensionsCallback
     */
    protected $xslImageDimensionsCallback;
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
        $this->mockXMLStreamWriter        = new stubDomXMLStreamWriter();
        $this->xslImageDimensionsCallback = new stubXslImageDimensionsCallback($this->mockXMLStreamWriter,
                                                                               TEST_SRC_PATH . '/resources',
                                                                               TEST_SRC_PATH . '/resources/common'
                                            );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xslImageDimensionsCallback->getClass()
                                                        ->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.docroot.path',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
        $this->assertTrue($refParams[2]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.docroot.path.common',
                            $refParams[2]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function getImageDimensionsMethodIsAnnotationWithXslMethod()
    {
        $this->assertTrue($this->xslImageDimensionsCallback
                               ->getClass()
                               ->getMethod('getImageDimensions')
                               ->hasAnnotation('XslMethod')
        );
    }

    /**
     * @test
     * @expectedException  stubXSLCallbackException
     */
    public function requestingDataForNonExistingImageThrowsCallbackException()
    {
        $this->xslImageDimensionsCallback->getImageDimensions('/img/doesNotExist.jpg');
    }

    /**
     * @test
     * @expectedException  stubXSLCallbackException
     */
    public function requestingDataForInvalidImageThrowsCallbackException()
    {
        $this->xslImageDimensionsCallback->getImageDimensions('/img/invalid.gif');
    }

    /**
     * @test
     */
    public function correctImageReturnedFromProjectPath()
    {
        $doc =  $this->xslImageDimensionsCallback->getImageDimensions('/img/stubbles.png');
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<image><width>132</width><height>113</height><type>PNG</type><mime>image/png</mime></image>\n", $doc->saveXML());
    }

    /**
     * @test
     * @since  1.5.0
     */
    public function correctImageReturnedFromCommonPathIfImageNotPresentInProjectPath()
    {
        $doc =  $this->xslImageDimensionsCallback->getImageDimensions('/img/stubbles2.png');
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<image><width>132</width><height>113</height><type>PNG</type><mime>image/png</mime></image>\n", $doc->saveXML());
    }
}
?>