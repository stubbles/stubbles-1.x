<?php
/**
 * Tests for net::stubbles::webapp::xml::route::stubAbstractProcessable.
 *
 * @package     stubbles
 * @subpackage  webapp_test
 * @version     $Id: stubAbstractProcessableTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::route::stubAbstractProcessable');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_test
 */
class TeststubAbstractProcessable extends stubAbstractProcessable
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
     * returns the request instance
     *
     * @return  stubRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * returns the context
     *
     * @return  array<string,mixed>
     */
    public function getContext()
    {
        return $this->context;
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
     * processes the processable
     *
     * @return  mixed
     */
    public function process()
    {
        // intentionally empty
    }
}
/**
 * Tests for net::stubbles::webapp::xml::route::stubAbstractProcessable
 *
 * @package     stubbles
 * @subpackage  webapp_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_route
 */
class stubAbstractProcessableTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubAbstractProcessable
     */
    protected $abstractProcessable;
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
        $this->mockRequest         = $this->getMock('stubRequest');
        $this->abstractProcessable = new TeststubAbstractProcessable($this->mockRequest);
    }

    /**
     * is still the request after construction
     *
     * @test
     */
    public function isRequestAfterConstruction()
    {
        $this->assertSame($this->mockRequest, $this->abstractProcessable->getRequest());
    }

    /**
     * context without prefix does not change request
     *
     * @test
     */
    public function contextWithoutPrefixDoesNotChangeRequest()
    {
        $this->assertSame($this->abstractProcessable,
                          $this->abstractProcessable->setContext(array('foo' => 'bar'))
        );
        $this->assertEquals(array('foo' => 'bar'), $this->abstractProcessable->getContext());
        $this->assertSame($this->mockRequest, $this->abstractProcessable->getRequest());
    }

    /**
     * context with prefix changes request
     *
     * @test
     */
    public function contextWithPrefixDoesChangesRequest()
    {
        $this->assertSame($this->abstractProcessable,
                          $this->abstractProcessable->setContext(array('foo'    => 'bar',
                                                                       'prefix' => 'baz'
                                                                 )
                                                      )
        );
        $this->assertEquals(array('foo'    => 'bar',
                                  'prefix' => 'baz'
                            ),
                            $this->abstractProcessable->getContext()
        );
        $request = $this->abstractProcessable->getRequest();
        $this->assertInstanceOf('stubRequestPrefixDecorator', $request);
    }

    /**
     * startup() does nothing
     *
     * @test
     */
    public function startupDoesNothing()
    {
        $this->abstractProcessable->startup();
    }

    /**
     * is available by default
     *
     * @test
     */
    public function isAvailableByDefault()
    {
        $this->assertTrue($this->abstractProcessable->isAvailable());
    }

    /**
     * cleanup() does nothing
     *
     * @test
     */
    public function cleanupDoesNothing()
    {
        $this->abstractProcessable->cleanup();
    }
}
?>