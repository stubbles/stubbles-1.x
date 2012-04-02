<?php
/**
 * Test for net::stubbles::service::rest::stubRestProcessor.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id: stubRestProcessorTestCase.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::stubRestProcessor');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.1.0
 */
class TestRestHandler extends stubBaseObject
{
    /**
     * @return  string
     * @RestMethod(requestMethod='get')
     */
    public function aGetMethod()
    {
        return 'a GET method';
    }

    /**
     * @return  string
     * @RestMethod(requestMethod='POST',
     *             formatter=net::stubbles::service::rest::format::stubJsonFormatter.class,
     *             errorFormatter=net::stubbles::service::rest::format::stubJsonFormatter.class)
     */
    public function aPostMethod()
    {
        return 'a POST method';
    }

    /**
     * @throws  stubException
     * @RestMethod(requestMethod='PUT')
     */
    public function aPutMethod()
    {
        throw new stubException('exception on PUT');
    }

    /**
     * @return  string
     * @RestMethod(requestMethod='DUMMY', path='d')
     */
    public function aDummyMethodForPathD()
    {
        return 'the DUMMY method for path d without arguments';
    }

    /**
     * @param   string  $e
     * @param   string  $f
     * @return  string
     * @RestMethod(requestMethod='DUMMY')
     */
    public function aDummyDefaultMethod($e, $f)
    {
        return 'the DUMMY default method with arguments ' . $e . ', ' . $f;
    }

    /**
     * @param   string  $e
     * @param   string  $f
     * @return  string
     * @RestMethod(requestMethod='DUMMY', path='a/b')
     */
    public function aDummyMethodForPathAb($e, $f)
    {
        return 'the DUMMY method for path a/b with arguments ' . $e . ', ' . $f;
    }

    /**
     * @param   string  $e
     * @param   string  $f
     * @return  string
     * @RestMethod(requestMethod='DUMMY', path='b/c/')
     */
    public function aDummyMethodForPathBc($e, $f)
    {
        return 'the DUMMY method for path b/c with arguments ' . $e . ', ' . $f;
    }

    /**
     * @param   string  $e
     * @param   string  $f
     * @return  string
     * @RestMethod(requestMethod='DUMMY', path='c;d', pathSeparator=';')
     */
    public function aDummyMethodForPathCd($e, $f)
    {
        return 'the DUMMY method for path c;d with arguments ' . $e . ', ' . $f;
    }

    /**
     * @param   string  $x
     * @return  string
     * @RestMethod(requestMethod='DUMMY2', path='z')
     */
    public function aDummy2MethodForPathZ($x)
    {
        return 'the DUMMY2 method for path z with arguments ' . $x;
    }

    /**
     * @param   stubDate  $date
     * @param   string    $f
     * @return  string
     * @since   1.3.0
     * @RestMethod(requestMethod='RESTARGUMENTFILTER')
     * @Filter[DateFilter]{date}()
     */
    public function aMethodWithFilterOnParameters(stubDate $date, $f)
    {
        return 'with filtered arguments ' . $date->format('Y-m-d') . ', ' . $f;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.1.0
 */
class TeststubRestProcessor extends stubRestProcessor
{
    /**
     * access to protected method
     *
     * @param   string                $type    'formatter' or 'errorFormatter'
     * @param   stubReflectionMethod  $method  optional
     * @return  stubFormatter
     */
    public function getFormatter($type, stubReflectionMethod $method = null)
    {
        return parent::getFormatter($type, $method);
    }
}
/**
 * Tests for net::stubbles::service::rest::stubRestProcessor.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.1.0
 * @group       service
 * @group       service_rest
 */
class stubRestProcessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestProcessor
     */
    protected $restProcessor;
    /**
     * mocked request instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequest;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
    /**
     * mocked response instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResponse;
    /**
     * mocked factory to create formatter instances
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFormatFactory;
    /**
     * mocked factory to create rest handler instances
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRestHandlerFactory;
    /**
     * mocked filter annotation reader facade
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAnnotationBasedFilterFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequest                      = $this->getMock('stubRequest');
        $this->mockSession                      = $this->getMock('stubSession');
        $this->mockResponse                     = $this->getMock('stubResponse');
        $this->mockFormatFactory                = $this->getMockWithoutConstructorCall('stubFormatFactory');
        $this->mockRestHandlerFactory           = $this->getMockWithoutConstructorCall('stubRestHandlerFactory');
        $this->mockAnnotationBasedFilterFactory = $this->getMockWithoutConstructorCall('stubAnnotationBasedFilterFactory');
        $this->restProcessor                    = new stubRestProcessor($this->mockRequest,
                                                                        $this->mockSession,
                                                                        $this->mockResponse,
                                                                        $this->mockFormatFactory,
                                                                        $this->mockRestHandlerFactory,
                                                                        $this->mockAnnotationBasedFilterFactory
                                                   );
    }

    /**
     * creates mock without calling the constructor
     *
     * @param   string                                   $className
     * @return  PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockWithoutConstructorCall($className)
    {
        return $this->getMock($className,
                              array(),
                              array(),
                              '',
                              false
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->restProcessor->getClass()
                                              ->getConstructor()
                                              ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     * @expectedException  stubProcessorException
     */
    public function nonExistingRestHandlerThrowsProcessorException()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(null));
        $errorFormatter = $this->getMock('stubErrorFormatter');
        $errorFormatter->expects($this->once())
                       ->method('getContentType')
                       ->will($this->returnValue('text/plain'));
        $errorFormatter->expects($this->once())
                       ->method('formatNotFoundError')
                       ->will($this->returnValue('handler not found'));
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($errorFormatter));
        $this->mockSession->expects($this->never())
                          ->method('putValue');
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('handler not found'));
        $this->restProcessor->startup(new stubUriRequest('/doesNotExist'));
    }

    /**
     * @test
     */
    public function startupCreatesRestHandler()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('GET'));
        $this->mockSession->expects($this->once())
                          ->method('putValue')
                          ->with($this->equalTo('net.stubbles.webapp.lastPage'), $this->equalTo('GET:test'));
        $this->assertSame($this->restProcessor, $this->restProcessor->startup(new stubUriRequest('/test')));
    }

    /**
     * @test
     */
    public function validRouteNameIsReturnedAfterSuccessfulStartup()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->assertEquals('test', $this->restProcessor->startup(new stubUriRequest('/test'))->getRouteName());
    }

    /**
     * @test
     */
    public function noSuitableMethodResultsInStatusCode405()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue(null));
        $mockErrorFormatter = $this->getMock('stubErrorFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($mockErrorFormatter));
        $mockErrorFormatter->expects($this->once())
                           ->method('formatMethodNotAllowedError')
                           ->with($this->equalTo('DELETE'),
                                  $this->equalTo(array('GET', 'POST', 'PUT', 'DUMMY', 'DUMMY2', 'RESTARGUMENTFILTER'))
                             )
                           ->will($this->returnValue('DELETE not valid, use GET, POST, PUT, DUMMY, DUMMY2 or RESTARGUMENTFILTER'));
        $this->assertSame($this->restProcessor, $this->restProcessor->startup(new stubUriRequest('/')));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DELETE'));
        $this->mockResponse->expects($this->at(0))
                           ->method('setStatusCode')
                           ->with($this->equalTo(405));
        $this->mockResponse->expects($this->at(1))
                           ->method('addHeader')
                           ->with($this->equalTo('Allow'), $this->equalTo('GET,POST,PUT,DUMMY,DUMMY2,RESTARGUMENTFILTER'));
        $this->mockResponse->expects($this->at(3))
                           ->method('write')
                           ->with($this->equalTo('DELETE not valid, use GET, POST, PUT, DUMMY, DUMMY2 or RESTARGUMENTFILTER'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function exceptionFromRestHandlerResultsInStatusCode500()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue(null));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $mockErrorFormatter = $this->getMock('stubErrorFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($mockErrorFormatter));
        $mockErrorFormatter->expects($this->once())
                           ->method('formatInternalServerError')
                           ->will($this->returnValue('Internal Server Error'));
        $this->assertSame($this->restProcessor, $this->restProcessor->startup(new stubUriRequest('/')));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('PUT'));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(500));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('Internal Server Error'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessful()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue(null));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('POST'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('a POST method'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessfulWithDummyDefault()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('e/f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('the DUMMY default method with arguments e, f'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessfulWithDummyPathA()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('a/b/e/f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('the DUMMY method for path a/b with arguments e, f'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessfulWithDummyPathB()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('b/c/e/f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('the DUMMY method for path b/c with arguments e, f'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessfulWithDummyPathC()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('c;d;e;f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('the DUMMY method for path c;d with arguments e, f'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processUnsuccessfulWithDummyPathC()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('c;d;e'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $mockErrorFormatter = $this->getMock('stubErrorFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($mockErrorFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockErrorFormatter->expects($this->once())
                           ->method('formatInternalServerError')
                           ->will($this->returnValue('formatted error message'));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(400));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted error message'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processSuccessfulWithDummyPathD()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('d'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('the DUMMY method for path d without arguments'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     */
    public function processUnsuccessfulWithDummyPathZ()
    {
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue(''));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $mockErrorFormatter = $this->getMock('stubErrorFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($mockErrorFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('DUMMY2'));
        $mockErrorFormatter->expects($this->once())
                           ->method('formatInternalServerError')
                           ->will($this->returnValue('formatted error message'));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(400));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted error message'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     * @since  1.3.0
     * @group  feature_234
     */
    public function processSuccessfulFilteringArguments()
    {
        $this->mockAnnotationBasedFilterFactory->expects($this->once())
                                               ->method('createForAnnotation')
                                               ->will($this->returnValue(new stubDateFilter($this->getMock('stubRequestValueErrorFactory'))));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('2010-08-20/f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('RESTARGUMENTFILTER'));
        $mockFormatter->expects($this->once())
                      ->method('format')
                      ->with($this->equalTo('with filtered arguments 2010-08-20, f'))
                      ->will($this->returnValue('formatted content'));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted content'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }

    /**
     * @test
     * @since  1.3.0
     * @group  feature_234
     */
    public function processUnsuccessfulFilteringArguments()
    {
        $mockRveFactory = $this->getMock('stubRequestValueErrorFactory');
        $mockRveFactory->expects($this->once())
                       ->method('create')
                       ->will($this->returnValue(new stubRequestValueError('DATE_INVALID', array())));
        $this->mockAnnotationBasedFilterFactory->expects($this->once())
                                               ->method('createForAnnotation')
                                               ->will($this->returnValue(new stubDateFilter($mockRveFactory)));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('createHandler')
                                     ->will($this->returnValue(new TestRestHandler()));
        $this->mockRestHandlerFactory->expects($this->once())
                                     ->method('getDispatchUri')
                                     ->will($this->returnValue('invalid/f'));
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createFormatter')
                                ->will($this->returnValue($mockFormatter));
        $mockErrorFormatter = $this->getMock('stubErrorFormatter');
        $this->mockFormatFactory->expects($this->once())
                                ->method('createErrorFormatter')
                                ->will($this->returnValue($mockErrorFormatter));
        $this->restProcessor->startup(new stubUriRequest('/'));
        $this->mockRequest->expects($this->any())
                          ->method('getMethod')
                          ->will($this->returnValue('RESTARGUMENTFILTER'));
        $mockErrorFormatter->expects($this->once())
                           ->method('formatInternalServerError')
                           ->will($this->returnValue('formatted error message'));
        $this->mockResponse->expects($this->once())
                           ->method('setStatusCode')
                           ->with($this->equalTo(400));
        $this->mockResponse->expects($this->once())
                           ->method('write')
                           ->with($this->equalTo('formatted error message'));
        $this->assertSame($this->restProcessor, $this->restProcessor->process());
    }
}
?>