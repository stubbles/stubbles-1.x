<?php
/**
 * Tests for net::stubbles::lang::stubArrayAccessor.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubArrayAccessorTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubArrayAccessor');
/**
 * Tests for net::stubbles::lang::stubArrayAccessor.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubArrayAccessorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubArrayAccessor
     */
    protected $arrayAccessorAssociative;
    /**
     * instance to test
     *
     * @var  stubArrayAccessor
     */
    protected $arrayAccessorNumeric;
    /**
     * instance to test
     *
     * @var  stubArrayAccessor
     */
    protected $arrayAccessorOne;
    /**
     * instance to test
     *
     * @var  stubArrayAccessor
     */
    protected $arrayAccessorEmpty;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->arrayAccessorAssociative = new stubArrayAccessor(array('first' => 'one', 'second' => 'two', 'third' => 'three'));
        $this->arrayAccessorNumeric     = new stubArrayAccessor(array('foo', 'bar', 'baz'));
        $this->arrayAccessorOne         = new stubArrayAccessor(array('onlyOne'));
        $this->arrayAccessorEmpty       = new stubArrayAccessor();
    }

    /**
     * assure that first value of array is returned
     *
     * @test
     */
    public function firstElement()
    {
        $this->assertEquals('one', $this->arrayAccessorAssociative->first());
        $this->assertEquals('foo', $this->arrayAccessorNumeric->first());
        $this->assertEquals('onlyOne', $this->arrayAccessorOne->first());
        $this->assertNull($this->arrayAccessorEmpty->first());
    }

    /**
     * assure that last value of array is returned
     *
     * @test
     */
    public function lastElement()
    {
        $this->assertEquals('three', $this->arrayAccessorAssociative->last());
        $this->assertEquals('baz', $this->arrayAccessorNumeric->last());
        $this->assertEquals('onlyOne', $this->arrayAccessorOne->last());
        $this->assertNull($this->arrayAccessorEmpty->last());
    }

    /**
     * assure that value at given offset is returned
     *
     * @test
     */
    public function atElement()
    {
        $this->assertEquals('two', $this->arrayAccessorAssociative->at('second'));
        $this->assertEquals('two', $this->arrayAccessorAssociative->offsetGet('second'));
        $this->assertEquals('two', $this->arrayAccessorAssociative['second']);
        $this->assertEquals('bar', $this->arrayAccessorNumeric->at(1));
        $this->assertEquals('bar', $this->arrayAccessorNumeric->offsetGet(1));
        $this->assertEquals('bar', $this->arrayAccessorNumeric[1]);
        $this->assertEquals('onlyOne', $this->arrayAccessorOne->at(0));
        $this->assertEquals('onlyOne', $this->arrayAccessorOne->offsetGet(0));
        $this->assertEquals('onlyOne', $this->arrayAccessorOne[0]);
    }

    /**
     * accessing a non-existing offset via at() triggers an exception
     *
     * @test
     * @expectedException  stubIllegalAccessException
     */
    public function atElementThrowsExceptionOnAt()
    {
        $this->arrayAccessorAssociative->at(2);
    }

    /**
     * accessing a non-existing offset via offsetGet() triggers an exception
     *
     * @test
     * @expectedException  stubIllegalAccessException
     */
    public function atElementThrowsExceptionOnOffsetGet()
    {
        $this->arrayAccessorAssociative->offsetGet(2);
    }

    /**
     * accessing a non-existing offset via direct access triggers an exception
     *
     * @test
     * @expectedException  stubIllegalAccessException
     */
    public function atElementThrowsExceptionOnDirectAccess()
    {
        $this->arrayAccessorAssociative[2];
    }

    /**
     * instance can be treated like an array
     *
     * @test
     */
    public function arrayBehaviour()
    {
        $this->assertEquals(3, count($this->arrayAccessorAssociative));
        $this->arrayAccessorAssociative['second'] = 'foo';
        $this->assertEquals('foo', $this->arrayAccessorAssociative->at('second'));
        $this->assertEquals('foo', $this->arrayAccessorAssociative->offsetGet('second'));
        $this->assertEquals('foo', $this->arrayAccessorAssociative['second']);
        
        $this->assertTrue($this->arrayAccessorAssociative->offsetExists('second'));
        $this->assertTrue(isset($this->arrayAccessorAssociative['second']));
        
        $this->assertEquals(3, count($this->arrayAccessorAssociative));
        
        unset($this->arrayAccessorAssociative['second']);
        $this->assertFalse($this->arrayAccessorAssociative->offsetExists('second'));
        $this->assertFalse(isset($this->arrayAccessorAssociative['second']));
        $this->assertEquals(2, count($this->arrayAccessorAssociative));
    }

    /**
     * make sure an iterator instance is returned
     *
     * @test
     */
    public function iteratorReturned()
    {
        $iterator = $this->arrayAccessorAssociative->getIterator();
        $this->assertInstanceOf('Iterator', $iterator);
    }

    /**
     * raw array should be returned
     *
     * @test
     */
    public function rawArray()
    {
        $this->assertEquals(array('first' => 'one', 'second' => 'two', 'third' => 'three'),
                            $this->arrayAccessorAssociative->toArray()
        );
        $this->assertEquals(array('foo', 'bar', 'baz'),
                            $this->arrayAccessorNumeric->toArray()
        );
        $this->assertEquals(array('onlyOne'),
                            $this->arrayAccessorOne->toArray()
        );
        $this->assertEquals(array(),
                            $this->arrayAccessorEmpty->toArray()
        );
    }

    /**
     * array should be replaced
     *
     * @test
     */
    public function replaceArray()
    {
        $this->assertEquals(array('first' => 'one', 'second' => 'two', 'third' => 'three'),
                            $this->arrayAccessorAssociative->toArray()
        );
        $this->arrayAccessorAssociative->replace($this->arrayAccessorNumeric->toArray());
        $this->assertEquals(array('foo', 'bar', 'baz'),
                            $this->arrayAccessorAssociative->toArray()
        );
    }

    /**
     * keys of wrapped array should be returned
     *
     * @test
     */
    public function getKeys()
    {
        $this->assertEquals(array('first', 'second', 'third'),
                            $this->arrayAccessorAssociative->getKeys()
        );
        $this->assertEquals(array(0, 1, 2),
                            $this->arrayAccessorNumeric->getKeys()
        );
        $this->assertEquals(array(0),
                            $this->arrayAccessorOne->getKeys()
        );
        $this->assertEquals(array(),
                            $this->arrayAccessorEmpty->getKeys()
        );
    }
}
?>