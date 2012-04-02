<?php
/**
 * Tests for net::stubbles::ipo::ioc::stubIpoBindingModule.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc_test
 * @version     $Id: stubIpoBindingModuleTestCase.php 3299 2011-12-28 16:59:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::ioc::stubIpoBindingModule');
/**
 * Tests for net::stubbles::ipo::ioc::stubIpoBindingModule.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc_test
 * @group       ipo
 * @group       ipo_ioc
 */
class stubIpoBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubIpoBindingModule
     */
    protected $ipoBindingModule;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->ipoBindingModule = new stubIpoBindingModule();
        $this->injector         = new stubInjector();
    }

    /**
     * all bindings should be set to instances
     *
     * @test
     */
    public function bindingsSetToInstances()
    {
        $this->ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubRequest'));
        $this->assertTrue($this->injector->hasBinding('stubSession'));
        $this->assertTrue($this->injector->hasBinding('stubResponse'));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.session.name'));
        $request = $this->injector->getInstance('stubRequest');
        $this->assertInstanceOf('stubWebRequest', $request);
        $this->assertInstanceOf('stubPHPSession', $this->injector->getInstance('stubSession'));
        $this->assertInstanceOf('stubBaseResponse', $this->injector->getInstance('stubResponse'));
        $this->assertEquals('PHPSESSID', $this->injector->getConstant('net.stubbles.session.name'));
    }

    /**
     * @test
     */
    public function correctHttpVersionOn1_0Requests()
    {
        $this->ipoBindingModule = $this->getMock('stubIpoBindingModule',
                                                 array('createRequest',
                                                       'createResponseInstance',
                                                       'createSession'
                                                 )
                                  );
        $mockRequest            = $this->getMock('stubRequest');
        $mockResponse           = $this->getMock('stubResponse');
        $this->ipoBindingModule->expects($this->once())
                               ->method('createRequest')
                               ->will($this->returnValue($mockRequest));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createResponseInstance')
                               ->with($this->equalTo('1.0'))
                               ->will($this->returnValue($mockResponse));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createSession')
                               ->will($this->returnValue($this->getMock('stubSession')));
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                            $this->getMock('stubFilterFactory'),
                                                                            'SERVER_PROTOCOL',
                                                                            'HTTP/1.0'
                                              )
                           )
                      );
        $mockRequest->expects($this->never())
                    ->method('cancel');
        $mockResponse->expects($this->never())
                     ->method('setStatusCode');
        $mockResponse->expects($this->never())
                     ->method('write');
        $this->ipoBindingModule->configure(new stubBinder($this->injector));
    }

    /**
     * @test
     */
    public function correctHttpVersionOn1_1Requests()
    {
        $this->ipoBindingModule = $this->getMock('stubIpoBindingModule',
                                                 array('createRequest',
                                                       'createResponseInstance',
                                                       'createSession'
                                                 )
                                  );
        $mockRequest            = $this->getMock('stubRequest');
        $mockResponse           = $this->getMock('stubResponse');
        $this->ipoBindingModule->expects($this->once())
                               ->method('createRequest')
                               ->will($this->returnValue($mockRequest));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createResponseInstance')
                               ->with($this->equalTo('1.1'))
                               ->will($this->returnValue($mockResponse));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createSession')
                               ->will($this->returnValue($this->getMock('stubSession')));
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                            $this->getMock('stubFilterFactory'),
                                                                            'SERVER_PROTOCOL',
                                                                            'HTTP/1.1'
                                              )
                           )
                      );
        $mockRequest->expects($this->never())
                    ->method('cancel');
        $mockResponse->expects($this->never())
                     ->method('setStatusCode');
        $mockResponse->expects($this->never())
                     ->method('write');
        $this->ipoBindingModule->configure(new stubBinder($this->injector));
    }

    /**
     * @test
     */
    public function cancelRequestOnUnsupportedHttpVersion()
    {
        $this->ipoBindingModule = $this->getMock('stubIpoBindingModule',
                                                 array('createRequest',
                                                       'createResponseInstance',
                                                       'createSession'
                                                 )
                                  );
        $mockRequest            = $this->getMock('stubRequest');
        $mockResponse           = $this->getMock('stubResponse');
        $this->ipoBindingModule->expects($this->once())
                               ->method('createRequest')
                               ->will($this->returnValue($mockRequest));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createResponseInstance')
                               ->with($this->equalTo('1.0'))
                               ->will($this->returnValue($mockResponse));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createSession')
                               ->will($this->returnValue($this->getMock('stubSession')));
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                            $this->getMock('stubFilterFactory'),
                                                                            'SERVER_PROTOCOL',
                                                                            'HTTP/0.9'
                                              )
                           )
                      );
        $mockRequest->expects($this->once())
                    ->method('cancel');
        $mockResponse->expects($this->once())
                     ->method('setStatusCode')
                     ->with($this->equalTo(505));
        $mockResponse->expects($this->once())
                     ->method('write');
        $this->ipoBindingModule->configure(new stubBinder($this->injector));
    }

    /**
     * @test
     */
    public function cancelRequestOnInvalidHttpVersion()
    {
        $this->ipoBindingModule = $this->getMock('stubIpoBindingModule',
                                                 array('createRequest',
                                                       'createResponseInstance',
                                                       'createSession'
                                                 )
                                  );
        $mockRequest            = $this->getMock('stubRequest');
        $mockResponse           = $this->getMock('stubResponse');
        $this->ipoBindingModule->expects($this->once())
                               ->method('createRequest')
                               ->will($this->returnValue($mockRequest));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createResponseInstance')
                               ->with($this->equalTo('1.0'))
                               ->will($this->returnValue($mockResponse));
        $this->ipoBindingModule->expects($this->once())
                               ->method('createSession')
                               ->will($this->returnValue($this->getMock('stubSession')));
        $mockRequest->expects($this->once())
                    ->method('readHeader')
                    ->will($this->returnValue(new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                                                            $this->getMock('stubFilterFactory'),
                                                                            'SERVER_PROTOCOL',
                                                                            'invalid'
                                              )
                           )
                      );
        $mockRequest->expects($this->once())
                    ->method('cancel');
        $mockResponse->expects($this->once())
                     ->method('setStatusCode')
                     ->with($this->equalTo(505));
        $mockResponse->expects($this->once())
                     ->method('write');
        $this->ipoBindingModule->configure(new stubBinder($this->injector));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function requestBindingSetToDifferentRequestClass()
    {
        $ipoBindingModule = new stubIpoBindingModule();
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->setRequestClassName('net::stubbles::ipo::request::stubModifiableWebRequest')
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubRequest'));
        $request = $this->injector->getInstance('stubRequest');
        $this->assertInstanceOf('stubModifiableWebRequest', $request);
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function requestBindingSetToModifiableRequestClass()
    {
        $ipoBindingModule = new stubIpoBindingModule();
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->useModifiableRequest()
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubRequest'));
        $request = $this->injector->getInstance('stubRequest');
        $this->assertInstanceOf('stubModifiableWebRequest', $request);
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function requestBindingSetToRedirectRequestClass()
    {
        $ipoBindingModule = new stubIpoBindingModule();
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->useRedirectRequest()
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubRequest'));
        $request = $this->injector->getInstance('stubRequest');
        $this->assertInstanceOf('stubRedirectRequest', $request);
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function bindingsSetToDifferentResponseClass()
    {
        $ipoBindingModule = new stubIpoBindingModule();
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->setResponseClassName('org::stubbles::test::DummyResponse')
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubResponse'));
        $this->assertInstanceOf('DummyResponse', $this->injector->getInstance('stubResponse'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function sessionBindingSetToDifferentSessionClass()
    {
        $ipoBindingModule = new stubIpoBindingModule('psessionid');
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->setSessionClassName('net::stubbles::ipo::session::stubNoneDurableSession')
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSession'));
        $this->session = $this->injector->getInstance('stubSession');
        $this->assertInstanceOf('stubNoneDurableSession', $this->session);
        $this->assertEquals('psessionid', $this->session->getName());
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function sessionBindingSetToDefaultSessionClass()
    {
        $ipoBindingModule = new stubIpoBindingModule();
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->useDefaultSession()
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSession'));
        $this->session = $this->injector->getInstance('stubSession');
        $this->assertInstanceOf('stubPHPSession', $this->session);
        $this->assertEquals('PHPSESSID', $this->session->getName());
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function sessionBindingSetToNoneDurableSessionClass()
    {
        $ipoBindingModule = new stubIpoBindingModule('psessionid');
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->useNoneDurableSession()
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSession'));
        $this->session = $this->injector->getInstance('stubSession');
        $this->assertInstanceOf('stubNoneDurableSession', $this->session);
        $this->assertEquals('psessionid', $this->session->getName());
    }

    /**
     * @test
     * @since  1.7.0
     */
    public function sessionBindingSetToNoneStoringSessionClass()
    {
        $ipoBindingModule = new stubIpoBindingModule('psessionid');
        $this->assertSame($ipoBindingModule,
                          $ipoBindingModule->useNoneStoringSession()
        );
        $ipoBindingModule->configure(new stubBinder($this->injector));
        $this->assertTrue($this->injector->hasBinding('stubSession'));
        $this->session = $this->injector->getInstance('stubSession');
        $this->assertInstanceOf('stubNoneStoringSession', $this->session);
        $this->assertEquals('psessionid', $this->session->getName());
    }

    /**
     * @test
     * @since  1.3.0
     */
    public function addedTypeFiltersAreBound()
    {
        stubIpoBindingModule::create()
                            ->addFilterForType('my::filter::ExampleFilter', 'example')
                            ->configure(new stubBinder($this->injector));
        $filterTypes = $this->injector->getConstant('net.stubbles.ipo.request.filter.types');
        $this->assertTrue(isset($filterTypes['example']));
        $this->assertEquals('my::filter::ExampleFilter', $filterTypes['example']);
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForIntAndInteger()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $filterTypes = $this->injector->getConstant('net.stubbles.ipo.request.filter.types');
        $this->assertArrayHasKey('int', $filterTypes);
        $this->assertArrayHasKey('integer', $filterTypes);
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForDoubleAndFloat()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $filterTypes = $this->injector->getConstant('net.stubbles.ipo.request.filter.types');
        $this->assertArrayHasKey('double', $filterTypes);
        $this->assertArrayHasKey('float', $filterTypes);
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForString()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('string',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForText()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('text',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForJson()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('json',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForPassword()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('password',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForHttpUrls()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('http',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForDates()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('date',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterTypeForMailAddresses()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('mail',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.types')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function addedFilterAnnotationReadersAreBound()
    {
        stubIpoBindingModule::create()
                            ->addFilterAnnotationReader('my::filter::annotation::ExampleFilterAnnotationReader',
                                                        'ExampleFilter'
                              )
                            ->configure(new stubBinder($this->injector));
        $filterAnnotationReaders = $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader');
        $this->assertTrue(isset($filterAnnotationReaders['ExampleFilter']));
        $this->assertEquals('my::filter::annotation::ExampleFilterAnnotationReader',
                            $filterAnnotationReaders['ExampleFilter']
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForBoolFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('BoolFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForDateFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('DateFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForFloatFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('FloatFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForHttpUrlFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('HTTPURLFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForIntegerFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('IntegerFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForMailFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('MailFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForPasswordFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('PasswordFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForPreselectFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('PreselectFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForStringFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('StringFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function hasDefaultFilterAnnotationReaderForTextFilterAnnotation()
    {
        stubIpoBindingModule::create()
                            ->configure(new stubBinder($this->injector));
        $this->assertArrayHasKey('TextFilter',
                                 $this->injector->getConstant('net.stubbles.ipo.request.filter.annotationreader')
        );
    }
}
?>