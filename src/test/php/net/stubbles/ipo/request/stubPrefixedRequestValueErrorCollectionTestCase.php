<?php
/**
 * Test for net::stubbles::ipo::request::stubPrefixedRequestValueErrorCollection.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubPrefixedRequestValueErrorCollectionTestCase.php 2637 2010-08-14 18:25:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubPrefixedRequestValueErrorCollection');
/**
 * Test for net::stubbles::ipo::request::stubPrefixedRequestValueErrorCollection.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubPrefixedRequestValueErrorCollectionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPrefixedRequestValueErrorCollection
     */
    protected $prefixedRequestValueErrorCollection;
    /**
     * mocked decorated instance
     *
     * @var  stubRequestValueErrorCollections
     */
    protected $mockRequestValueErrorCollection;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorCollection     = $this->getMock('stubRequestValueErrorCollection');
        $this->prefixedRequestValueErrorCollection = new stubPrefixedRequestValueErrorCollection($this->mockRequestValueErrorCollection,
                                                                                                 'test'
                                                     );
    }

    /**
     * @test
     */
    public function addAddsValueErrorToDecoratedInstanceWithPrefix()
    {
        $requestValueError = new stubRequestValueError('bar', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('add')
                                              ->with($this->equalTo($requestValueError), $this->equalTo('test_foo'))
                                              ->will($this->returnValue($requestValueError));
        $this->assertSame($requestValueError,
                          $this->prefixedRequestValueErrorCollection->add($requestValueError,
                                                                           'foo'
                                                                       )
        );
    }

    /**
     * @test
     */
    public function existForCallsDecoratedInstanceWithPrefix()
    {
        $requestValueError = new stubRequestValueError('bar', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('existFor')
                                              ->with($this->equalTo('test_foo'))
                                              ->will($this->returnValue(true));
        $this->assertTrue($this->prefixedRequestValueErrorCollection->existFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdCallsDecoratedInstanceWithPrefix()
    {
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('existForWithId')
                                              ->with($this->equalTo('test_foo'), $this->equalTo('id'))
                                              ->will($this->returnValue(true));
        $this->assertTrue($this->prefixedRequestValueErrorCollection->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForCallsDecoratedInstanceWithPrefix()
    {
        $requestValueError = new stubRequestValueError('bar', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('getFor')
                                              ->with($this->equalTo('test_foo'))
                                              ->will($this->returnValue($requestValueError));
        $this->assertSame($requestValueError,
                          $this->prefixedRequestValueErrorCollection->getFor('foo')
        );
    }

    /**
     * @test
     */
    public function getForWithIdCallsDecoratedInstanceWithPrefix()
    {
        $requestValueError = new stubRequestValueError('bar', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('getForWithId')
                                              ->with($this->equalTo('test_foo'), $this->equalTo('id'))
                                              ->will($this->returnValue($requestValueError));
        $this->assertSame($requestValueError,
                          $this->prefixedRequestValueErrorCollection->getForWithId('foo', 'id')
        );
    }

    /**
     * @test
     */
    public function getReturnsEmptyErrorListIfDecoratedInstanceHasNoErrors()
    {
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('exist')
                                              ->will($this->returnValue(false));
        $this->assertEquals(array(),
                            $this->prefixedRequestValueErrorCollection->get()
        );
    }

    /**
     * @test
     */
    public function getReturnsOnlyErrorsFromDecoratedInstanceWithGivenPrefix()
    {
        $requestValueError1 = new stubRequestValueError('id1', array());
        $requestValueError2 = new stubRequestValueError('id2', array());
        $requestValueError3 = new stubRequestValueError('id3', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('exist')
                                              ->will($this->returnValue(true));
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('get')
                                              ->will($this->returnValue(array('test_foo' => array('id1' => $requestValueError1),
                                                                              'bar'      => array('id2' => $requestValueError2),
                                                                              'test_baz' => array('id3' => $requestValueError3),
                                                                        )
                                                     )
                                                );
        $this->assertEquals(array('foo' => array('id1' => $requestValueError1),
                                  'baz' => array('id3' => $requestValueError3),
                            ),
                            $this->prefixedRequestValueErrorCollection->get()
        );
    }

    /**
     * @test
     */
    public function countCountsOnlyErrorsFromDecoratedInstanceWithGivenPrefix()
    {
        $requestValueError1 = new stubRequestValueError('id1', array());
        $requestValueError2 = new stubRequestValueError('id2', array());
        $requestValueError3 = new stubRequestValueError('id3', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('exist')
                                              ->will($this->returnValue(true));
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('get')
                                              ->will($this->returnValue(array('test_foo' => array('id1' => $requestValueError1),
                                                                              'bar'      => array('id2' => $requestValueError2),
                                                                              'test_baz' => array('id3' => $requestValueError3),
                                                                        )
                                                     )
                                                );
        $this->assertEquals(2,
                            $this->prefixedRequestValueErrorCollection->count()
        );
    }

    /**
     * @test
     */
    public function existReturnsTrueOnlyIfErrorsFromDecoratedInstanceWithGivenPrefixExist()
    {
        $requestValueError1 = new stubRequestValueError('id1', array());
        $requestValueError2 = new stubRequestValueError('id2', array());
        $requestValueError3 = new stubRequestValueError('id3', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('exist')
                                              ->will($this->returnValue(true));
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('get')
                                              ->will($this->returnValue(array('test_foo' => array('id1' => $requestValueError1),
                                                                              'bar'      => array('id2' => $requestValueError2),
                                                                              'test_baz' => array('id3' => $requestValueError3),
                                                                        )
                                                     )
                                                );
        $this->assertTrue($this->prefixedRequestValueErrorCollection->exist());
    }

    /**
     * @test
     */
    public function existReturnsFalseIfThereAreNoErrorsFromDecoratedInstanceWithGivenPrefix()
    {
        $requestValueError1 = new stubRequestValueError('id1', array());
        $requestValueError2 = new stubRequestValueError('id2', array());
        $requestValueError3 = new stubRequestValueError('id3', array());
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('exist')
                                              ->will($this->returnValue(true));
        $this->mockRequestValueErrorCollection->expects($this->once())
                                              ->method('get')
                                              ->will($this->returnValue(array('foo' => array('id1' => $requestValueError1),
                                                                              'bar' => array('id2' => $requestValueError2),
                                                                              'baz' => array('id3' => $requestValueError3),
                                                                        )
                                                     )
                                                );
        $this->assertFalse($this->prefixedRequestValueErrorCollection->exist());
    }
}
?>