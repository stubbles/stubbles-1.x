<?php
/**
 * Test for net::stubbles::ipo::request::stubDefaultRequestValueErrorCollection.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @version     $Id: stubDefaultRequestValueErrorCollectionTestCase.php 2637 2010-08-14 18:25:37Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubDefaultRequestValueErrorCollection');
/**
 * Test for net::stubbles::ipo::request::stubDefaultRequestValueErrorCollection.
 *
 * @package     stubbles
 * @subpackage  ipo_request_test
 * @since       1.3.0
 * @group       ipo
 * @group       ipo_request
 */
class stubDefaultRequestValueErrorCollectionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultRequestValueErrorCollection
     */
    protected $defaultRequestValueErrorCollection;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultRequestValueErrorCollection = new stubDefaultRequestValueErrorCollection();
    }

    /**
     * @test
     */
    public function hasNoErrorsInitially()
    {
        $this->assertFalse($this->defaultRequestValueErrorCollection->exist());
        $this->assertEquals(0, $this->defaultRequestValueErrorCollection->count());
        $this->assertEquals(array(), $this->defaultRequestValueErrorCollection->get());
    }

    /**
     * @test
     */
    public function addErrorForSingleRequestValue()
    {
        $requestValueError = new stubRequestValueError('id', array());
        $this->assertSame($requestValueError,
                          $this->defaultRequestValueErrorCollection->add($requestValueError,
                                                                         'foo'
                                                                     )
        );

        $this->assertTrue($this->defaultRequestValueErrorCollection->exist());
        $this->assertTrue($this->defaultRequestValueErrorCollection->existFor('foo'));
        $this->assertTrue($this->defaultRequestValueErrorCollection->existForWithId('foo', 'id'));
        $this->assertEquals(1, $this->defaultRequestValueErrorCollection->count());
        $this->assertEquals(array('foo' => array('id' => $requestValueError)), $this->defaultRequestValueErrorCollection->get());
        $this->assertEquals(array('id' => $requestValueError), $this->defaultRequestValueErrorCollection->getFor('foo'));
        $this->assertEquals($requestValueError, $this->defaultRequestValueErrorCollection->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function addSameErrorForSameValueNameDoesNotResultInTwoErrorsOfSameKind()
    {
        $requestValueError = new stubRequestValueError('id', array());
        $this->assertSame($requestValueError,
                          $this->defaultRequestValueErrorCollection->add($requestValueError,
                                                                         'foo'
                                                                     )
        );
        $this->assertSame($requestValueError,
                          $this->defaultRequestValueErrorCollection->add($requestValueError,
                                                                         'foo'
                                                                     )
        );

        $this->assertTrue($this->defaultRequestValueErrorCollection->exist());
        $this->assertEquals(1, $this->defaultRequestValueErrorCollection->count());
        $this->assertEquals(array('foo' => array('id' => $requestValueError)), $this->defaultRequestValueErrorCollection->get());
    }

    /**
     * @test
     */
    public function existForReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->defaultRequestValueErrorCollection->existFor('foo'));
    }

    /**
     * @test
     */
    public function getForReturnsEmptyArrayIfNoErrorAddedBefore()
    {
        $this->assertEquals(array(), $this->defaultRequestValueErrorCollection->getFor('foo'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorAddedBefore()
    {
        $this->assertFalse($this->defaultRequestValueErrorCollection->existForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorAddedBefore()
    {
        $this->assertNull($this->defaultRequestValueErrorCollection->getForWithId('foo', 'id'));
    }

    /**
     * @test
     */
    public function existForWithIdReturnsFalseIfNoErrorOfThisNameAddedBefore()
    {
        $requestValueError = new stubRequestValueError('id', array());
        $this->assertSame($requestValueError,
                          $this->defaultRequestValueErrorCollection->add($requestValueError,
                                                                         'foo'
                                                                     )
        );
        $this->assertFalse($this->defaultRequestValueErrorCollection->existForWithId('foo', 'baz'));
    }

    /**
     * @test
     */
    public function getForWithIdReturnsNullIfNoErrorOfThisNameAddedBefore()
    {
        $requestValueError = new stubRequestValueError('id', array());
        $this->assertSame($requestValueError,
                          $this->defaultRequestValueErrorCollection->add($requestValueError,
                                                                         'foo'
                                                                     )
        );
        $this->assertNull($this->defaultRequestValueErrorCollection->getForWithId('foo', 'baz'));
    }
}
?>