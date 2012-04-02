<?php
/**
 * Tests for net::stubbles::webapp::xml::route::stubAbstractXmlFormProcessable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route_test
 * @version     $Id: stubAbstractXmlFormProcessableTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubAbstractXmlFormProcessable');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route_test
 */
class TeststubAbstractXmlFormProcessable extends stubAbstractXmlFormProcessable
{
    /**
     * constructor
     *
     * @param  stubRequest  $request
     */
    public function __construct(stubRequest $request)
    {
        $this->request = $request;
    }

    /**
     * checks whether processable is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        // intentionally empty
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        // intentionally empty
    }

    /**
     * processes the page element
     *
     * @return  mixed  content for page element
     */
    public function process()
    {
        // intentionally empty
    }

    /**
     * helper method to call protected method
     *
     * @since  1.2.0
     */
    public function disableFormValueSerialization()
    {
        parent::disableFormValueSerialization();
    }

    /**
     * helper method to call protected method
     *
     * @since  1.2.0
     */
    public function enableFormValueSerialization()
    {
        parent::enableFormValueSerialization();
    }
}
/**
 * Tests for net::stubbles::webapp::xml::route::stubAbstractXmlFormProcessable.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_route
 */
class stubAbstractXmlFormProcessableTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubAbstractXmlFormProcessable
     */
    protected $abstractXmlFormProcessable;
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
        $this->mockRequest                = $this->getMock('stubRequest');
        $this->abstractXmlFormProcessable = new TeststubAbstractXmlFormProcessable($this->mockRequest);
    }

    /**
     * helper method to create request values
     *
     * @param   string                     $value
     * @return  stubFilteringRequestValue
     */
    protected function createFilteringRequestValue($value)
    {
        return new stubFilteringRequestValue($this->getMock('stubRequestValueErrorCollection'),
                                             $this->getMock('stubFilterFactory'),
                                             'dummy',
                                             $value
               );
    }

    /**
     * @test
     */
    public function getFormValuesIsEnabledByDefault()
    {
        $this->mockRequest->expects($this->once())
                          ->method('getParamNames')
                          ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->mockRequest->expects($this->at(1))
                          ->method('readParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('fooValue')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('barValue')));
        $this->mockRequest->expects($this->at(3))
                          ->method('readParam')
                          ->with($this->equalTo('baz'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bazValue')));
        $this->assertEquals(array('foo' => 'fooValue',
                                  'bar' => 'barValue',
                                  'baz' => 'bazValue'
                            ),
                            $this->abstractXmlFormProcessable->getFormValues()
        );
    }

    /**
     * @test
     * @since  1.2.0
     */
    public function getFormValuesReturnsEmptyArrayIfDisabled()
    {
        $this->abstractXmlFormProcessable->disableFormValueSerialization();
        $this->mockRequest->expects($this->never())
                          ->method('getParamNames');
        $this->assertEquals(array(),
                            $this->abstractXmlFormProcessable->getFormValues()
        );
    }

    /**
     * @test
     */
    public function getFormValuesIsEnabledAfterDisabling()
    {
        $this->abstractXmlFormProcessable->disableFormValueSerialization();
        $this->abstractXmlFormProcessable->enableFormValueSerialization();
        $this->mockRequest->expects($this->once())
                          ->method('getParamNames')
                          ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->mockRequest->expects($this->at(1))
                          ->method('readParam')
                          ->with($this->equalTo('foo'))
                          ->will($this->returnValue($this->createFilteringRequestValue('fooValue')));
        $this->mockRequest->expects($this->at(2))
                          ->method('readParam')
                          ->with($this->equalTo('bar'))
                          ->will($this->returnValue($this->createFilteringRequestValue('barValue')));
        $this->mockRequest->expects($this->at(3))
                          ->method('readParam')
                          ->with($this->equalTo('baz'))
                          ->will($this->returnValue($this->createFilteringRequestValue('bazValue')));
        $this->assertEquals(array('foo' => 'fooValue',
                                  'bar' => 'barValue',
                                  'baz' => 'bazValue'
                            ),
                            $this->abstractXmlFormProcessable->getFormValues()
        );
    }
}
?>