<?php
/**
 * Test for net::stubbles::ipo::interceptors::stubPropertyBasedInterceptorInitializer.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @version     $Id: stubPropertyBasedInterceptorInitializerTestCase.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPropertyBasedInterceptorInitializer');
/**
 * Test for net::stubbles::ipo::interceptors::stubPropertyBasedInterceptorInitializer.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors_test
 * @since       1.1.0
 * @deprecated
 * @group       ipo
 * @group       ipo_interceptors
 */
class stubPropertyBasedInterceptorInitializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPropertyBasedInterceptorInitializer
     */
    protected $propertyBasedInterceptorInitializer;
    /**
     * mocked injector
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockInjector                        = $this->getMock('stubInjector');
        $this->propertyBasedInterceptorInitializer = new stubPropertyBasedInterceptorInitializer($this->mockInjector, TEST_SRC_PATH . '/resources');
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $constructor = $this->propertyBasedInterceptorInitializer->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $params = $constructor->getParameters();
        $this->assertTrue($params[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path', $params[1]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function getPreInterceptorsWithoutCallingInitThrowsIllegalStateException()
    {
        $this->propertyBasedInterceptorInitializer->getPreInterceptors();
    }

    /**
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function getPostInterceptorsWithoutCallingInitThrowsIllegalStateException()
    {
        $this->propertyBasedInterceptorInitializer->getPostInterceptors();
    }

    /**
     * @test
     */
    public function getPreInterceptors()
    {
        $fooPreInterceptor = $this->getMock('stubPreInterceptor');
        $barPreInterceptor = $this->getMock('stubPreInterceptor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::test::FooPreInterceptor'))
                           ->will($this->returnValue($fooPreInterceptor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::test::BarPreInterceptor'))
                           ->will($this->returnValue($barPreInterceptor));
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->init()
        );
        $preInterceptors = $this->propertyBasedInterceptorInitializer->getPreInterceptors();
        $this->assertEquals(2, count($preInterceptors));
        $this->assertSame($fooPreInterceptor, $preInterceptors[0]);
        $this->assertSame($barPreInterceptor, $preInterceptors[1]);
    }

    /**
     * @test
     */
    public function getPostInterceptors()
    {
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->init()
        );
        $this->assertEquals(array(), $this->propertyBasedInterceptorInitializer->getPostInterceptors());
    }

    /**
     * @test
     */
    public function getPreInterceptorsWithOtherDescriptor()
    {
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->setDescriptor('interceptors-other')
        );
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->init()
        );
        $this->assertEquals(array(), $this->propertyBasedInterceptorInitializer->getPreInterceptors());
    }

    /**
     * @test
     */
    public function getPostInterceptorsWithOtherDescriptor()
    {
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->setDescriptor('interceptors-other')
        );
        $fooPreInterceptor = $this->getMock('stubPreInterceptor');
        $barPreInterceptor = $this->getMock('stubPreInterceptor');
        $this->mockInjector->expects($this->at(0))
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::test::FooPreInterceptor'))
                           ->will($this->returnValue($fooPreInterceptor));
        $this->mockInjector->expects($this->at(1))
                           ->method('getInstance')
                           ->with($this->equalTo('net::stubbles::test::BarPreInterceptor'))
                           ->will($this->returnValue($barPreInterceptor));
        $this->assertSame($this->propertyBasedInterceptorInitializer,
                          $this->propertyBasedInterceptorInitializer->init()
        );
        $preInterceptors = $this->propertyBasedInterceptorInitializer->getPostInterceptors();
        $this->assertEquals(2, count($preInterceptors));
        $this->assertSame($fooPreInterceptor, $preInterceptors[0]);
        $this->assertSame($barPreInterceptor, $preInterceptors[1]);
    }
}
?>