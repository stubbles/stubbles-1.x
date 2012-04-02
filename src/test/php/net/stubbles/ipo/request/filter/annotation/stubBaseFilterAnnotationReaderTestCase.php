<?php
/**
 * Base class for filter annotation reader test case classes.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilterBuilder',
                      'net::stubbles::reflection::annotations::stubGenericAnnotation'
);
/**
 * Base class for filter annotation reader test case classes.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_annotation_test
 */
abstract class stubBaseFilterAnnotationReaderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * mocked filter factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFilterFactory;
    /**
     * annotation to create filter from
     *
     * @var  stubGenericAnnotation
     */
    protected $annotation;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockFilterFactory    = $this->getMock('stubFilterFactory');
        $this->annotation           = new stubGenericAnnotation();
        $this->annotation->required = false;
        $this->annotation->setAnnotationName('BoolFilter');
        $this->doSetUp();
    }

    /**
     * creates rest of test environment
     */
    protected abstract function doSetUp();

    /**
     * prepares filter factory
     */
    protected function prepareFilterFactory($type, stubFilter $filter)
    {
        $this->mockFilterFactory->expects($this->once())
                                ->method('createForType')
                                ->with($this->equalTo($type))
                                ->will($this->returnValue(new stubFilterBuilder($filter,
                                                                                $this->getMock('stubRequestValueErrorFactory')
                                                          )
                                              )
                                  );
    }

    /**
     * @test
     */
    public function annotationFilterReaderHasSingletonAnnotation()
    {
        $this->assertTrue($this->getTestInstance()->getClass()->hasAnnotation('Singleton'));
    }

    /**
     * asserts that created filter is same as original filter
     *
     * @param  stubFilter                    $filter
     * @param  stubFilter|stubFilterBuilder  $createdFilter
     */
    protected function assertOriginalFilter(stubFilter $filter, $createdFilter)
    {
        $this->assertSame($filter, $createdFilter);
    }

    /**
     * @test
     */
    public function createsOriginalFilterIfRequiredDisabled()
    {
        $this->annotation->required = false;
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertOriginalFilter($filter, $createdFilter->getDecoratedFilter());
    }

    /**
     * @test
     */
    public function createsFilterWithRequiredDecoratorByDefault()
    {
        $this->annotation = new stubGenericAnnotation();
        $this->annotation->setAnnotationName('BoolFilter');
        $this->doSetUp();
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertInstanceOf('stubRequiredFilterDecorator',
                                $createdFilter->getDecoratedFilter()
        );
        $this->assertOriginalFilter($filter,
                                    $createdFilter->getDecoratedFilter()
                                                  ->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function createsFilterWithRequiredDecoratorIfExplicitlyRequired()
    {
        $this->annotation->required = true;
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertInstanceOf('stubRequiredFilterDecorator',
                                $createdFilter->getDecoratedFilter()
        );
        $this->assertOriginalFilter($filter,
                                    $createdFilter->getDecoratedFilter()
                                                  ->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function createsFilterWithDefaultValueDecorator()
    {
        $this->annotation->required     = false;
        $this->annotation->defaultValue = true;
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertInstanceOf('stubDefaultValueFilterDecorator',
                                $createdFilter->getDecoratedFilter()
        );
        $this->assertOriginalFilter($filter,
                                    $createdFilter->getDecoratedFilter()
                                                  ->getDecoratedFilter()
        );
    }

    /**
     * @test
     */
    public function createsFilterWithRequiredAndDefaultDecorator()
    {
        $this->annotation->required     = true;
        $this->annotation->defaultValue = true;
        $filter = $this->createFilter();
        $this->prepareFilterFactory($this->getFilterType(), $filter);
        $createdFilter = $this->getTestInstance()->createFilter($this->mockFilterFactory,
                                                                $this->annotation
                                                   );
        $this->assertInstanceOf('stubFilterBuilder', $createdFilter);
        $this->assertInstanceOf('stubDefaultValueFilterDecorator',
                                $createdFilter->getDecoratedFilter()
        );
        $this->assertInstanceOf('stubRequiredFilterDecorator',
                                $createdFilter->getDecoratedFilter()
                                              ->getDecoratedFilter()
        );
        $this->assertOriginalFilter($filter,
                                    $createdFilter->getDecoratedFilter()
                                                  ->getDecoratedFilter()
                                                  ->getDecoratedFilter()
        );
    }

    /**
     * returns instance to test
     *
     * @return  stubFilterAnnotationReader
     */
    protected abstract function getTestInstance();

    /**
     * creates a filter for required and default value tests
     *
     * @return  stubFilter
     */
    protected abstract function createFilter();

    /**
     * returns filter type
     *
     * @return  string
     */
    protected abstract function getFilterType();
}
?>