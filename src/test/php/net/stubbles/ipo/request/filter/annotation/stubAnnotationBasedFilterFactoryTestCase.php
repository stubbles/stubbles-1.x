<?php
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubAnnotationBasedFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::annotation::stubAnnotationBasedFilterFactory',
                      'net::stubbles::reflection::annotations::stubGenericAnnotation'
);
/**
 * Tests for net::stubbles::ipo::request::filter::annotation::stubAnnotationBasedFilterFactory.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 * @group       ipo_request_filter_annotation
 */
class stubAnnotationBasedFilterFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationBasedFilterFactory
     */
    protected $annotationBasedFilterFactory;
    /**
     * mocked filter factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFilterFactory;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockFilterFactory            = $this->getMock('stubFilterFactory');
        $this->mockInjector                 = $this->getMock('stubInjector');
        $this->annotationBasedFilterFactory = new stubAnnotationBasedFilterFactory($this->mockFilterFactory,
                                                                                   $this->mockInjector,
                                                                                   array('FooFilter' => 'example::FooFilter')
                                              );
    }

    /**
     * @test
     */
    public function annotationsPresentOnClass()
    {
        $this->assertTrue($this->annotationBasedFilterFactory->getClass()
                                                             ->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->annotationBasedFilterFactory->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[2]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.ipo.request.filter.annotationreader',
                            $parameters[2]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     */
    public function throwsConfigurationExceptionIfRequestedAnnotationTypeHasNoReader()
    {
        $annotation = new stubGenericAnnotation();
        $annotation->setAnnotationName('DoesNotExistFilter');
        $this->annotationBasedFilterFactory->createForAnnotation($annotation);
    }

    /**
     * @test
     * @expectedException  stubConfigurationException
     */
    public function throwsConfigurationExceptionIfRequestedAnnotationTypeIsNotAnInstanceOfFilterAnnotationReader()
    {
        $mockFilterAnnotationReader = $this->getMock('stubFilterAnnotationReader');
        $this->mockInjector->expects(($this->once()))
                           ->method('getInstance')
                           ->will($this->returnValue(new stdClass()));
        $annotation = new stubGenericAnnotation();
        $annotation->setAnnotationName('FooFilter');
        $this->annotationBasedFilterFactory->createForAnnotation($annotation);
    }

    /**
     * @test
     */
    public function createsFilter()
    {
        $mockFilterAnnotationReader = $this->getMock('stubFilterAnnotationReader');
        $this->mockInjector->expects(($this->once()))
                           ->method('getInstance')
                           ->will($this->returnValue($mockFilterAnnotationReader));
        $annotation = new stubGenericAnnotation();
        $annotation->setAnnotationName('FooFilter');
        $mockFilter = $this->getMock('stubFilter');
        $mockFilterAnnotationReader->expects($this->once())
                                   ->method('createFilter')
                                   ->with($this->equalTo($this->mockFilterFactory),
                                          $this->equalTo($annotation)
                                     )
                                   ->will($this->returnValue($mockFilter));
        $this->assertSame($mockFilter,
                          $this->annotationBasedFilterFactory->createForAnnotation($annotation)
        );
    }
}
?>