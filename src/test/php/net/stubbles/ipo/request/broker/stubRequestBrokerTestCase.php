<?php
/**
 * Tests for net::stubbles::ipo::request::broker::stubRequestBroker.
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker_test
 * @version     $Id: stubRequestBrokerTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::broker::stubRequestBroker');
require_once dirname(__FILE__) . '/TestBrokerClasses.php';
/**
 * Tests for net::stubbles::ipo::request::broker::stubRequestBroker.
 *
 * @package     stubbles
 * @subpackage  ipo_request_broker_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_broker
 */
class stubRequestBrokerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRequestBroker
     */
    protected $requestBroker;
    /**
     * mocked filter factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockAnnotationBasedFilterFactory;
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
        $this->mockAnnotationBasedFilterFactory = $this->getMock('stubAnnotationBasedFilterFactory',
                                                                 array(),
                                                                 array(),
                                                                 '',
                                                                 false
                                                  );
        $this->requestBroker = new stubRequestBroker($this->mockAnnotationBasedFilterFactory);
        $this->mockRequest   = $this->getMock('stubRequest');
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalObjectThrowsIllegalArgumentException()
    {
        $this->requestBroker->process($this->mockRequest, 'foo');
    }

    /**
     * helper method to create request value instances
     *
     * @param   string                     $value
     * @return  stubFilteringRequestValue
     */
    protected function createFilteringRequestValue($value)
    {
        return new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                             $this->getMock('stubFilterFactory'),
                                             'paramName',
                                             $value
               );
    }
    /**
     * @test
     */
    public function withClassThatIsNotInstanceOfStubObject()
    {
        $mockFilter = $this->getMock('stubFilter');
        $mockFilter->expects($this->any())
                   ->method('execute')
                   ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockAnnotationBasedFilterFactory->expects($this->exactly(2))
                                               ->method('createForAnnotation')
                                               ->will($this->returnValue($mockFilter));
        $this->mockRequest->expects($this->at(0))
                          ->method('readParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('foo')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bar')));
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->exactly(2))
                                        ->method('existFor')
                                        ->will($this->returnValue(false));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $testClass = new TestBrokerClass();
        $this->requestBroker->process($this->mockRequest, $testClass);
        $this->assertEquals('foo', $testClass->foo);
        $this->assertEquals('bar', $testClass->getBar());
        $this->assertNull($testClass->getBaz());
        $this->assertNull(TestBrokerClass::$dummy);
    }

    /**
     * @test
     */
    public function withClassThatIsInstanceOfStubObject()
    {
        $mockFilter = $this->getMock('stubFilter');
        $mockFilter->expects($this->any())
                   ->method('execute')
                   ->will($this->onConsecutiveCalls('foo', 'bar'));
        $this->mockAnnotationBasedFilterFactory->expects($this->exactly(2))
                                               ->method('createForAnnotation')
                                               ->will($this->returnValue($mockFilter));
        $this->mockRequest->expects($this->at(0))
                          ->method('readParam')
                          ->with($this->equalTo('prefix_foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('foo')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('prefix_bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bar')));
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->exactly(2))
                                        ->method('existFor')
                                        ->will($this->returnValue(false));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $testClass = new TestBrokerObject();
        $this->requestBroker->process($this->mockRequest, $testClass, 'prefix_');
        $this->assertEquals('foo', $testClass->foo);
        $this->assertEquals('bar', $testClass->getBar());
        $this->assertNull($testClass->getBaz());
        $this->assertNull(TestBrokerObject::$dummy);
    }

    /**
     * @test
     */
    public function withClassThatIsNotInstanceOfStubObjectAndFilterOverruling()
    {
        $this->mockAnnotationBasedFilterFactory->expects($this->never())
                                               ->method('createForAnnotation');
        $overrules = array('foo' => $this->getMock('stubFilter'),
                           'bar' => $this->getMock('stubFilter')
                     );
        $overrules['foo']->expects($this->once())
                         ->method('execute')
                         ->will($this->returnValue('foo'));
        $overrules['bar']->expects($this->once())
                         ->method('execute')
                         ->will($this->returnValue('bar'));
        $this->mockRequest->expects($this->at(0))
                          ->method('readParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('foo')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bar')));
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->exactly(2))
                                        ->method('existFor')
                                        ->will($this->returnValue(false));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $testClass = new TestBrokerClass();
        $this->requestBroker->process($this->mockRequest, $testClass, '', $overrules);
        $this->assertEquals('foo', $testClass->foo);
        $this->assertEquals('bar', $testClass->getBar());
        $this->assertNull($testClass->getBaz());
        $this->assertNull(TestBrokerClass::$dummy);
    }

    /**
     * @test
     */
    public function withClassThatIsInstanceOfStubObjectAndFilterOverruling()
    {
        $this->mockAnnotationBasedFilterFactory->expects($this->never())
                                               ->method('createForAnnotation');
        $overrules = array('prefix_foo' => $this->getMock('stubFilter'),
                           'prefix_bar' => $this->getMock('stubFilter')
                     );
        $overrules['prefix_foo']->expects($this->once())
                                ->method('execute')
                                ->will($this->returnValue('foo'));
        $overrules['prefix_bar']->expects($this->once())
                                ->method('execute')
                                ->will($this->returnValue('bar'));
        $this->mockRequest->expects($this->at(0))
                          ->method('readParam')
                          ->with($this->equalTo('prefix_foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('foo')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('prefix_bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bar')));
        $mockRequestValueErrorCollection = $this->getMock('stubRequestValueErrorCollection');
        $mockRequestValueErrorCollection->expects($this->exactly(2))
                                        ->method('existFor')
                                        ->will($this->returnValue(false));
        $this->mockRequest->expects($this->exactly(2))
                          ->method('paramErrors')
                          ->will($this->returnValue($mockRequestValueErrorCollection));
        $testClass = new TestBrokerObject();
        $this->requestBroker->process($this->mockRequest, $testClass, 'prefix_', $overrules);
        $this->assertEquals('foo', $testClass->foo);
        $this->assertEquals('bar', $testClass->getBar());
        $this->assertNull($testClass->getBaz());
        $this->assertNull(TestBrokerObject::$dummy);
    }
}
?>