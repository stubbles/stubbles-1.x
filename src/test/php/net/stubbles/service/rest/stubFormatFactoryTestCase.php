<?php
/**
 * Test for net::stubbles::service::rest::stubFormatFactory.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id: stubFormatFactoryTestCase.php 3204 2011-11-02 16:12:02Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::stubFormatFactory');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 */
class stubFormatFactoryTestCaseRestHandler extends stubBaseObject
{
    /**
     * @RestMethod(requestMethod='GET')
     */
    public function handleSomething()
    {
        // intentionally empty
    }
    
    /**
     * @RestMethod(requestMethod='GET',
     *             formatter=org::stubbles::test::rest::stubBazFormatter.class,
     *             errorFormatter=org::stubbles::test::rest::stubBazFormatter.class)
     */
    public function handleSomethingOther()
    {
        // intentionally empty
    }
}
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::service::rest::stubFormatFactory.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.7.0
 * @group       service
 * @group       service_rest
 */
class stubFormatFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFormatFactory
     */
    protected $formatFactory;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;
    /**
     * rest.ini config file mock
     *
     * @var  vfsStreamFile
     */
    protected $configFile;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }

        $this->configFile    = vfsStream::newFile('rest.ini')->withContent('[formatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"

[errorFormatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"')->at(vfsStream::setup());
        $this->mockInjector  = $this->getMock('stubInjector');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
    }

    /**
     * @test
     */
    public function formatterClassFoundInHeader()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubDummyFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createFormatter(stubAcceptHeader::parse('doesNotExist,dummy'),
                                                                new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                                                )
        );
    }

    /**
     * @test
     */
    public function errorFormatterClassFoundInHeader()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubDummyFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createErrorFormatter(stubAcceptHeader::parse('doesNotExist,dummy'),
                                                                     new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                                                )
        );
    }

    /**
     * @test
     */
    public function annotatedFormatterClassIsAcceptable()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createFormatter(stubAcceptHeader::parse('doesNotExist,baz'),
                                                                new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther')
                          )
        );
    }

    /**
     * @test
     */
    public function annotatedErrorFormatterClassIsAcceptable()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createErrorFormatter(stubAcceptHeader::parse('doesNotExist,baz'),
                                                                     new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther')
                          )
        );
    }

    /**
     * @test
     */
    public function annotatedFormatterClassIsAcceptableWhenNoAcceptHeaderIsSet()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createFormatter(new stubAcceptHeader(),
                                                                new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther')
                          )
        );
    }

    /**
     * @test
     */
    public function annotatedErrorFormatterClassIsAcceptableWhenNoAcceptHeaderIsSet()
    {
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertSame($mockFormatter,
                          $this->formatFactory->createErrorFormatter(new stubAcceptHeader(),
                                                                     new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther')
                          )
        );
    }

    /**
     * @test
     */
    public function formatterClassNotFoundInHeaderReturnsNull()
    {
        $this->configFile->setContent('[formatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"

[errorFormatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertNull($this->formatFactory->createFormatter(stubAcceptHeader::parse('doesNotExist,blub'),
                                                                new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                          )
        );
    }

    /**
     * @test
     */
    public function errorFormatterClassNotFoundInHeaderReturnsNull()
    {
        $this->configFile->setContent('[formatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"

[errorFormatter]
baz="org::stubbles::test::rest::stubBazFormatter"
dummy="org::stubbles::test::rest::stubDummyFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertNull($this->formatFactory->createErrorFormatter(stubAcceptHeader::parse('doesNotExist,blub'),
                                                                     new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                          )
        );
    }

    /**
     * @test
     */
    public function formatterClassNotFoundInHeaderButVoidFormatterConfiguredReturnsVoidFormatter()
    {
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertInstanceOf('stubVoidFormatter',
                                $this->formatFactory->createFormatter(stubAcceptHeader::parse('doesNotExist,blub'),
                                                                      new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                                )
        );
    }

    /**
     * @test
     */
    public function errorFormatterClassNotFoundInHeaderButVoidFormatterConfiguredReturnsVoidFormatter()
    {
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertInstanceOf('stubVoidFormatter',
                                $this->formatFactory->createErrorFormatter(stubAcceptHeader::parse('doesNotExist,blub'),
                                                                           new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething')
                                )
        );
    }

    /**
     * @test
     */
    public function getSupportedMimeTypesReturnsListOfMimeTypesFromConfig()
    {
        $this->configFile->setContent('[formatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"

[errorFormatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertEquals(array('dummy',
                                  '*/*'
                            ),
                            $this->formatFactory->getSupportedMimeTypes(new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething'))
        );
    }

    /**
     * @test
     */
    public function getSupportedMimeTypesReturnsListOfMimeTypesFromConfigAndMethod()
    {
        $this->configFile->setContent('[formatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"

[errorFormatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertEquals(array('dummy',
                                  '*/*',
                                  'baz'
                            ),
                            $this->formatFactory->getSupportedMimeTypes(new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther'))
        );
    }

    /**
     * @test
     */
    public function getSupportedErrorMimeTypesReturnsListOfMimeTypesFromConfig()
    {
        $this->configFile->setContent('[formatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"

[errorFormatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $this->assertEquals(array('dummy',
                                  '*/*'
                            ),
                            $this->formatFactory->getSupportedErrorMimeTypes(new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomething'))
        );
    }

    /**
     * @test
     */
    public function getSupportedErrorMimeTypesReturnsListOfMimeTypesFromConfigAndMethod()
    {
        $this->configFile->setContent('[formatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"

[errorFormatter]
dummy="org::stubbles::test::rest::stubDummyFormatter"
*/*="net::stubbles::service::rest::stubVoidFormatter"');
        $this->formatFactory = new stubFormatFactory($this->mockInjector, vfsStream::url('root'));
        $mockFormatter = $this->getMock('stubFormatter');
        $mockFormatter->expects($this->any())
                      ->method('getContentType')
                      ->will($this->returnValue('baz'));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::rest::stubBazFormatter'))
                           ->will($this->returnValue($mockFormatter));
        $this->assertEquals(array('dummy',
                                  '*/*',
                                  'baz'
                            ),
                            $this->formatFactory->getSupportedErrorMimeTypes(new stubReflectionMethod('stubFormatFactoryTestCaseRestHandler', 'handleSomethingOther'))
        );
    }
}
?>