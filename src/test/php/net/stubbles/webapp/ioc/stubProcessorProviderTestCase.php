<?php
/**
 * Test for net::stubbles::webapp::ioc::stubProcessorProvider.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::webapp::ioc::stubProcessorProvider',
                      'net::stubbles::webapp::cache::stubWebsiteCache',
                      'net::stubbles::webapp::auth::stubAuthHandler'
);
/**
 * Test for net::stubbles::webapp::ioc::stubProcessorProvider.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 * @group       webapp
 * @group       webapp_ioc
 */
class stubProcessorProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubProcessorProvider
     */
    protected $processorProvider;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector      = $this->getMock('stubInjector');
        $this->processorProvider = $this->createProcessorProvider(false);
    }

    /**
     * helper method to create instance to test
     *
     * @param   bool                   $authEnabled
     * @return  stubProcessorProvider
     */
    protected function createProcessorProvider($authEnabled)
    {
        return new stubProcessorProvider($this->mockInjector,
                                         $this->getMock('stubRequest'),
                                         $this->getMock('stubResponse'),
                                         array('example' => 'my::ExampleProcessor'),
                                         $authEnabled
               );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->processorProvider->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[3]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.webapp.processor.map',
                            $parameters[3]->getAnnotation('Named')->getName()
        );
        $this->assertTrue($parameters[4]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.webapp.auth',
                            $parameters[4]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetModeMethod()
    {
        $setModeMethod = $this->processorProvider->getClass()->getMethod('setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function getWithoutProcessorNameThrowsIllegalArgumentException()
    {
        $this->processorProvider->get();
    }

    /**
     * @test
     * @expectedException  stubRuntimeException
     */
    public function getWithUnknownProcessorNameThrowsRuntimeException()
    {
        $this->processorProvider->get('unknown');
    }

    /**
     * @test
     */
    public function createsUndecoratedProcessorIfAuthAndCacheDisabled()
    {
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->any())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(false));
        $this->processorProvider->setMode($mockMode);
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->assertSame($processor,
                          $this->processorProvider->get('example')
        );
    }

    /**
     * @test
     */
    public function createsAuthProcessorIfAuthEnabledAndCacheDisabled()
    {
        $processorProvider = $this->createProcessorProvider(true);
        $mockMode          = $this->getMock('stubMode');
        $mockMode->expects($this->any())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(false));
        $processorProvider->setMode($mockMode);
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('stubAuthHandler'))
                           ->will($this->returnValue($this->getMock('stubAuthHandler')));
        $this->assertInstanceOf('stubAuthProcessor',
                                $processorProvider->get('example')
        );
    }

    /**
     * @test
     */
    public function createsCachingProcessorIfAuthDisabledAndCacheEnabled()
    {
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->any())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(true));
        $this->processorProvider->setMode($mockMode);
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('stubWebsiteCache'))
                           ->will($this->returnValue($this->getMock('stubWebsiteCache')));
        $this->assertInstanceOf('stubCachingProcessor',
                                $this->processorProvider->get('example')
        );
    }

    /**
     * @test
     */
    public function createsCachingProcessorIfAuthDisabledAndNoModeSet()
    {
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('stubWebsiteCache'))
                           ->will($this->returnValue($this->getMock('stubWebsiteCache')));
        $this->assertInstanceOf('stubCachingProcessor',
                                $this->processorProvider->get('example')
        );
    }

    /**
     * @test
     */
    public function createsCachingProcessorIfAuthAndCacheEnabled()
    {
        $processorProvider = $this->createProcessorProvider(true);
        $mockMode          = $this->getMock('stubMode');
        $mockMode->expects($this->any())
                 ->method('isCacheEnabled')
                 ->will($this->returnValue(true));
        $processorProvider->setMode($mockMode);
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('stubAuthHandler'))
                           ->will($this->returnValue($this->getMock('stubAuthHandler')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubWebsiteCache'))
                           ->will($this->returnValue($this->getMock('stubWebsiteCache')));
        $this->assertInstanceOf('stubCachingProcessor',
                                $processorProvider->get('example')
        );
    }

    /**
     * @test
     */
    public function createsCachingProcessorIfAuthEnabledAndNoModeSet()
    {
        $processorProvider = $this->createProcessorProvider(true);
        $processor = $this->getMock('stubProcessor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('my::ExampleProcessor'))
                           ->will($this->returnValue($processor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('stubAuthHandler'))
                           ->will($this->returnValue($this->getMock('stubAuthHandler')));
        $this->mockInjector->expects($this->at(2))
                           ->method('getInstance')
                           ->with($this->equalTo('stubWebsiteCache'))
                           ->will($this->returnValue($this->getMock('stubWebsiteCache')));
        $this->assertInstanceOf('stubCachingProcessor',
                                $processorProvider->get('example')
        );
    }
}
?>