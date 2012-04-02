<?php
/**
 * Test for net::stubbles::ipo::ioc::stubFilterTypeProvider.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc_test
 * @version     $Id: stubFilterTypeProviderTestCase.php 3115 2011-03-30 16:28:24Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::ioc::stubFilterTypeProvider');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::ipo::ioc::stubFilterTypeProvider.
 *
 * @package     stubbles
 * @subpackage  ipo_ioc_test
 * @since       1.6.0
 * @group       ipo
 * @group       ipo_ioc
 */
class stubFilterTypeProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFilterTypeProvider
     */
    protected $filterTypeProvider;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->filterTypeProvider = new stubFilterTypeProvider();
    }

    /**
     * @test
     * @expectedException  stubBindingException
     */
    public function getWithNullNameThrowsBindingException()
    {
        $this->filterTypeProvider->get();
    }

    /**
     * @test
     * @expectedException  stubBindingException
     */
    public function getWithUnknownNameThrowsBindingException()
    {
        $this->filterTypeProvider->get('doesNotExist');
    }

    /**
     * @test
     */
    public function hasDefaultTypeFilters()
    {
        $typeFilters = $this->filterTypeProvider->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('bool', $typeFilters);
        $this->assertArrayHasKey('int', $typeFilters);
        $this->assertArrayHasKey('integer', $typeFilters);
        $this->assertArrayHasKey('double', $typeFilters);
        $this->assertArrayHasKey('float', $typeFilters);
        $this->assertArrayHasKey('string', $typeFilters);
        $this->assertArrayHasKey('text', $typeFilters);
        $this->assertArrayHasKey('json', $typeFilters);
        $this->assertArrayHasKey('password', $typeFilters);
        $this->assertArrayHasKey('http', $typeFilters);
        $this->assertArrayHasKey('date', $typeFilters);
        $this->assertArrayHasKey('mail', $typeFilters);
    }

    /**
     * @test
     */
    public function hasDefaultAnnotationReaders()
    {
        $annotationReaders = $this->filterTypeProvider->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('BoolFilter', $annotationReaders);
        $this->assertArrayHasKey('DateFilter', $annotationReaders);
        $this->assertArrayHasKey('FloatFilter', $annotationReaders);
        $this->assertArrayHasKey('HTTPURLFilter', $annotationReaders);
        $this->assertArrayHasKey('IntegerFilter', $annotationReaders);
        $this->assertArrayHasKey('MailFilter', $annotationReaders);
        $this->assertArrayHasKey('PasswordFilter', $annotationReaders);
        $this->assertArrayHasKey('PreselectFilter', $annotationReaders);
        $this->assertArrayHasKey('StringFilter', $annotationReaders);
        $this->assertArrayHasKey('TextFilter', $annotationReaders);
    }

    /**
     * @test
     */
    public function canAddTypeFilter()
    {
        $typeFilters = $this->filterTypeProvider->addFilterForType('my::FooFilter', 'foo')
                                                ->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('foo', $typeFilters);
        $this->assertEquals('my::FooFilter', $typeFilters['foo']);
    }

    /**
     * @test
     */
    public function canAddAnnotationReader()
    {
        $annotationReaders = $this->filterTypeProvider->addFilterAnnotationReader('my::FooFilterAnnotationReader',
                                                                                  'FooFilter'
                                                        )
                                                      ->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('FooFilter', $annotationReaders);
        $this->assertEquals('my::FooFilterAnnotationReader', $annotationReaders['FooFilter']);
    }

    /**
     * @test
     */
    public function canOverwriteDefaultTypeFilter()
    {
        $typeFilters = $this->filterTypeProvider->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('string', $typeFilters);
        $this->assertEquals('net::stubbles::ipo::request::filter::stubStringFilter', $typeFilters['string']);

        $typeFilters = $this->filterTypeProvider->addFilterForType('my::StringFilter', 'string')
                                                ->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('string', $typeFilters);
        $this->assertEquals('my::StringFilter', $typeFilters['string']);
    }

    /**
     * @test
     */
    public function canOverwriteDefaultAnnotationReader()
    {
        $annotationReaders = $this->filterTypeProvider->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('StringFilter', $annotationReaders);
        $this->assertEquals('net::stubbles::ipo::request::filter::annotation::stubStringFilterAnnotationReader',
                            $annotationReaders['StringFilter']
        );

        $annotationReaders = $this->filterTypeProvider->addFilterAnnotationReader('my::StringFilterAnnotationReader',
                                                                                  'StringFilter'
                                                        )
                                                      ->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('StringFilter', $annotationReaders);
        $this->assertEquals('my::StringFilterAnnotationReader',
                            $annotationReaders['StringFilter']
        );
    }

    /**
     * helper method to create a mocked resource loader instance
     *
     * @return  PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockResourceLoader()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }

        vfsStream::setup();
        vfsStream::newFile('ipo/filter.ini')
                 ->at(vfsStreamWrapper::getRoot())
                 ->withContent("[filter]
example=my::ExampleFilter
string=my::StringFilter

[annotationReader]
ExampleFilter=my::ExampleFilterAnnotationReader
StringFilter=my::StringFilterAnnotationReader
");
        $mockResourceLoader = $this->getMock('stubResourceLoader');
        $mockResourceLoader->expects($this->once())
                           ->method('getStarResourceUris')
                           ->will($this->returnValue(array(vfsStream::url('root/ipo/filter.ini'))));
        return $mockResourceLoader;
    }

    /**
     * @test
     */
    public function addsTypeFiltersFromStarFiles()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $typeFilters        = $filterTypeProvider->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('example', $typeFilters);
        $this->assertEquals('my::ExampleFilter', $typeFilters['example']);
    }

    /**
     * @test
     */
    public function typeFiltersFromStarFilesOverwriteDefaultTypeFilters()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $typeFilters        = $filterTypeProvider->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('string', $typeFilters);
        $this->assertEquals('my::StringFilter', $typeFilters['string']);
    }

    /**
     * @test
     */
    public function canOverwriteFiltersFromStarFiles()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $typeFilters        = $filterTypeProvider->addFilterForType('my::FooFilter', 'example')
                                                 ->get(stubFilterTypeProvider::FILTER_TYPES_NAME);
        $this->assertArrayHasKey('example', $typeFilters);
        $this->assertEquals('my::FooFilter', $typeFilters['example']);
    }

    /**
     * @test
     */
    public function addsAnnotationReadersFromStarFiles()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $annotationReaders  = $filterTypeProvider->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('ExampleFilter', $annotationReaders);
        $this->assertEquals('my::ExampleFilterAnnotationReader',
                            $annotationReaders['ExampleFilter']
        );
    }

    /**
     * @test
     */
    public function annotationReadersFromStarFilesOverwriteDefaultTypeFilters()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $annotationReaders  = $filterTypeProvider->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('StringFilter', $annotationReaders);
        $this->assertEquals('my::StringFilterAnnotationReader',
                            $annotationReaders['StringFilter']
        );
    }

    /**
     * @test
     */
    public function canOverwriteAnnotationReadersFromStarFiles()
    {
        $filterTypeProvider = new stubFilterTypeProvider($this->createMockResourceLoader());
        $annotationReaders  = $filterTypeProvider->addFilterAnnotationReader('my::FooFilterAnnotationReader',
                                                                             'ExampleFilter'
                                                   )
                                                 ->get(stubFilterTypeProvider::ANNOTATIONREADER_NAME);
        $this->assertArrayHasKey('ExampleFilter', $annotationReaders);
        $this->assertEquals('my::FooFilterAnnotationReader',
                            $annotationReaders['ExampleFilter']
        );
    }
}
?>