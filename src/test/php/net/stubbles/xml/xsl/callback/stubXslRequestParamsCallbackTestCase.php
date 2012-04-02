<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslRequestParamsCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslRequestParamsCallbackTestCase.php 2972 2011-02-07 18:32:07Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslRequestParamsCallback',
                      'net::stubbles::xml::stubDomXMLStreamWriter'
);
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslRequestParamsCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslRequestParamsCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXslRequestParamsCallback
     */
    protected $xslRequestParamsCallback;
    /**
     * instance to test
     *
     * @var  stubDomXMLStreamWriter
     */
    protected $mockXMLStreamWriter;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXMLStreamWriter      = new stubDomXMLStreamWriter();
        $this->mockRequest              = $this->getMock('stubRequest');
        $this->xslRequestParamsCallback = new stubXslRequestParamsCallback($this->mockXMLStreamWriter,
                                                                           $this->mockRequest
                                          );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->xslRequestParamsCallback
                               ->getClass()
                               ->getConstructor()
                               ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function getQueryStringMethodIsAnnotatedAsXslMethod()
    {
        $this->assertTrue($this->xslRequestParamsCallback
                               ->getClass()
                               ->getMethod('getQueryString')
                               ->hasAnnotation('XslMethod')
        );
    }

    /**
     * @test
     */
    public function createsQueryString()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->with($this->equalTo('QUERY_STRING'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'QUERY_STRING',
                                                                                  'processor=xml&page=article&article_id=89&=&test=foo'
                                                    )
                                 )
                            );
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<requestParams>&amp;article_id=89&amp;test=foo</requestParams>\n",
                            $this->xslRequestParamsCallback->getQueryString()
                                                           ->saveXML()
        );
    }

    /**
     * @test
     */
    public function createsQueryStringWithArray()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->with($this->equalTo('QUERY_STRING'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'QUERY_STRING',
                                                                                  'test[foo]=bar&test[bar]=baz'
                                                    )
                                 )
                            );
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<requestParams>test[foo]=bar&amp;test[bar]=baz</requestParams>\n",
                            $this->xslRequestParamsCallback->getQueryString()
                                                           ->saveXML()
        );
    }

    /**
     * @test
     */
    public function createsCompleteQueryStringWithArrayButWithoutProcessorAndPage()
    {
        $this->mockRequest->expects($this->once())
                          ->method('readHeader')
                          ->with($this->equalTo('QUERY_STRING'))
                          ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                                  $this->getMock('stubFilterFactory'),
                                                                                  'QUERY_STRING',
                                                                                  'processor=xml&page=article&test[foo]=bar&test[bar]=baz'
                                                    )
                                 )
                            );
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<requestParams>&amp;test[foo]=bar&amp;test[bar]=baz</requestParams>\n",
                            $this->xslRequestParamsCallback->getQueryString()
                                                           ->saveXML()
        );
    }
}
?>