<?php
/**
 * Test for net::stubbles::service::rest::format::stubXmlFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @version     $Id: stubXmlFormatterTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubXmlFormatter');
/**
 * Tests for net::stubbles::service::rest::format::stubXmlFormatter.
 *
 * @package     stubbles
 * @subpackage  service_rest_format_test
 * @since       1.1.0
 * @group       service
 * @group       service_rest
 * @group       service_rest_format
 */
class stubXmlFormatterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlFormatter
     */
    protected $xmlFormatter;
    /**
     * mocked xml serializer facade
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockXmlSerializerFacade;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXmlSerializerFacade = $this->getMock('stubXmlSerializerFacade',
                                                        array(),
                                                        array(),
                                                        '',
                                                        false
                                         );
        $this->xmlFormatter            = new stubXmlFormatter($this->mockXmlSerializerFacade);
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $this->assertTrue($this->xmlFormatter->getClass()
                                             ->getConstructor()
                                             ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function correctContentType()
    {
        $this->assertEquals('text/xml',
                            $this->xmlFormatter->getContentType()
        );
    }

    /**
     * @test
     */
    public function formatsXml()
    {
        $this->mockXmlSerializerFacade->expects($this->once())
                                      ->method('serializeToXml')
                                      ->with($this->equalTo('value'))
                                      ->will($this->returnValue('<xml/>'));
        $this->assertEquals('<xml/>',
                            $this->xmlFormatter->format('value')
        );
    }

    /**
     * @test
     */
    public function formatNotFoundErrorReturnsXml()
    {
        $this->mockXmlSerializerFacade->expects($this->once())
                                      ->method('serializeToXml')
                                      ->with($this->equalTo(array('error' => 'Given resource could not be found.')))
                                      ->will($this->returnValue('<xml/>'));
        $this->assertEquals('<xml/>',
                            $this->xmlFormatter->formatNotFoundError()
        );
    }

    /**
     * @test
     */
    public function formatMethodNotAllowedErrorReturnsXml()
    {
        $this->mockXmlSerializerFacade->expects($this->once())
                                      ->method('serializeToXml')
                                      ->with($this->equalTo(array('error' => 'The given request method PUT is not valid. Please use GET, POST, DELETE.')))
                                      ->will($this->returnValue('<xml/>'));
        $this->assertEquals('<xml/>',
                            $this->xmlFormatter->formatMethodNotAllowedError('put', array('GET', 'POST', 'DELETE'))
        );
    }

    /**
     * @test
     */
    public function formatInternalServerErrorReturnsXml()
    {
        $this->mockXmlSerializerFacade->expects($this->once())
                                      ->method('serializeToXml')
                                      ->with($this->equalTo(array('error' => 'Internal Server Error: Error message')))
                                      ->will($this->returnValue('<xml/>'));
        $this->assertEquals('<xml/>',
                            $this->xmlFormatter->formatInternalServerError(new Exception('Error message'))
        );
    }
}
?>