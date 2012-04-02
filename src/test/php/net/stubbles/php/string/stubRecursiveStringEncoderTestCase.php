<?php
/**
 * Tests for net::stubbles::php::string::stubRecursiveStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @version     $Id: stubRecursiveStringEncoderTestCase.php 3273 2011-12-09 15:07:44Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubRecursiveStringEncoder',
                      'net::stubbles::php::string::stubUTF8Encoder'
);
/**
 * Tests for net::stubbles::php::string::stubRecursiveStringEncoder.
 *
 * @package     stubbles
 * @subpackage  php_string_test
 * @group       php
 * @group       php_string
 */
class stubRecursiveStringEncoderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRecursiveStringEncoder
     */
    protected $recursiveEncoder;
    /**
     * a mocked decorated encoder
     *
     * @var  SimpleMock
     */
    protected $decoratedEncoder;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->decoratedEncoder = $this->getMock('stubStringEncoder');
        $this->recursiveEncoder = new stubRecursiveStringEncoder($this->decoratedEncoder);
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function encodeInt()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('encode')
                               ->with($this->equalTo(2))
                               ->will($this->returnValue(2));
        $this->assertEquals(2, $this->recursiveEncoder->encode(2));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function encodeFloat()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('encode')
                               ->with($this->equalTo(3.13))
                               ->will($this->returnValue(3.13));
        $this->assertEquals(3.13, $this->recursiveEncoder->encode(3.13));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function encodeBool()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('encode')
                               ->with($this->equalTo(true))
                               ->will($this->returnValue(true));
        $this->assertEquals(true, $this->recursiveEncoder->encode(true));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function encodeString()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('encode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->recursiveEncoder->encode('foo'));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function decodeInt()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('decode')
                               ->with($this->equalTo(2))
                               ->will($this->returnValue(2));
        $this->assertEquals(2, $this->recursiveEncoder->decode(2));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function decodeFloat()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('decode')
                               ->with($this->equalTo(3.13))
                               ->will($this->returnValue(3.13));
        $this->assertEquals(3.13, $this->recursiveEncoder->decode(3.13));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function decodeBool()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('decode')
                               ->with($this->equalTo(true))
                               ->will($this->returnValue(true));
        $this->assertTrue($this->recursiveEncoder->decode(true));
    }

    /**
     * assure that scalar types are passed through to the decorated encoder
     *
     * @test
     */
    public function decodeString()
    {
        $this->decoratedEncoder->expects($this->once())
                               ->method('decode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->assertEquals('foo', $this->recursiveEncoder->decode('foo'));
    }

    /**
     * assert that null is returned as is
     *
     * @test
     */
    public function nullValue()
    {
        $this->decoratedEncoder->expects($this->never())->method('encode');
        $this->decoratedEncoder->expects($this->never())->method('decode');
        $this->assertNull($this->recursiveEncoder->encode(null));
        $this->assertNull($this->recursiveEncoder->decode(null));
    }

    /**
     * assert that an array is handled correct
     *
     * @test
     */
    public function encodeArray()
    {
        $array = array('foo', null, array(10, 20), 'bar');
        $this->decoratedEncoder->expects($this->at(0))
                               ->method('encode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->decoratedEncoder->expects($this->at(1))
                               ->method('encode')
                               ->with($this->equalTo(10))
                               ->will($this->returnValue(10));
        $this->decoratedEncoder->expects($this->at(2))
                               ->method('encode')
                               ->with($this->equalTo(20))
                               ->will($this->returnValue(20));
        $this->decoratedEncoder->expects($this->at(3))
                               ->method('encode')
                               ->with($this->equalTo('bar'))
                               ->will($this->returnValue('bar'));
        $this->assertEquals($array, $this->recursiveEncoder->encode($array));
    }

    /**
     * assert that an array is handled correct
     *
     * @test
     */
    public function decodeArray()
    {
        $array = array('foo', null, array(10, 20), 'bar');
        $this->decoratedEncoder->expects($this->at(0))
                               ->method('decode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->decoratedEncoder->expects($this->at(1))
                               ->method('decode')
                               ->with($this->equalTo(10))
                               ->will($this->returnValue(10));
        $this->decoratedEncoder->expects($this->at(2))
                               ->method('decode')
                               ->with($this->equalTo(20))
                               ->will($this->returnValue(20));
        $this->decoratedEncoder->expects($this->at(3))
                               ->method('decode')
                               ->with($this->equalTo('bar'))
                               ->will($this->returnValue('bar'));
        $this->assertEquals($array, $this->recursiveEncoder->decode($array));
    }

    /**
     * assert that an object is handled correct
     *
     * @test
     */
    public function encodeObject()
    {
        $object        = new stdClass();
        $object->foo   = 'foo';
        $object->null  = null;
        $object->array = array(10, 20);
        $object->bar   = 'bar';
        $this->decoratedEncoder->expects($this->at(0))
                               ->method('encode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->decoratedEncoder->expects($this->at(1))
                               ->method('encode')
                               ->with($this->equalTo(10))
                               ->will($this->returnValue(10));
        $this->decoratedEncoder->expects($this->at(2))
                               ->method('encode')
                               ->with($this->equalTo(20))
                               ->will($this->returnValue(20));
        $this->decoratedEncoder->expects($this->at(3))
                               ->method('encode')
                               ->with($this->equalTo('bar'))
                               ->will($this->returnValue('bar'));
        $this->assertEquals($object, $this->recursiveEncoder->encode($object));
    }

    /**
     * assert that an object is handled correct
     *
     * @test
     */
    public function decodeObject()
    {
        $object        = new stdClass();
        $object->foo   = 'foo';
        $object->null  = null;
        $object->array = array(10, 20);
        $object->bar   = 'bar';
        $this->decoratedEncoder->expects($this->at(0))
                               ->method('decode')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue('foo'));
        $this->decoratedEncoder->expects($this->at(1))
                               ->method('decode')
                               ->with($this->equalTo(10))
                               ->will($this->returnValue(10));
        $this->decoratedEncoder->expects($this->at(2))
                               ->method('decode')
                               ->with($this->equalTo(20))
                               ->will($this->returnValue(20));
        $this->decoratedEncoder->expects($this->at(3))
                               ->method('decode')
                               ->with($this->equalTo('bar'))
                               ->will($this->returnValue('bar'));
        $this->assertEquals($object, $this->recursiveEncoder->decode($object));
    }

    /**
     * Once in a time there was a net::stubbles::util::encoding::stubEncodingHelper
     * class, these tests make sure that legacy code will still work if it has
     * been changed to use the new decorator and UTF-8 encoder class.
     *
     * @test
     */
    public function encodingHelperLegacy()
    {
        $recursiveUTF8Encoder = new stubRecursiveStringEncoder(new stubUTF8Encoder());
        // strings
        $this->assertEquals('Foobar', $recursiveUTF8Encoder->encode('Foobar'));
        $this->assertEquals('Hähnchen', $recursiveUTF8Encoder->encode(utf8_decode('Hähnchen')));

        // numbers
        $this->assertEquals(12345, $recursiveUTF8Encoder->encode(12345));
        $this->assertEquals(-43.23, $recursiveUTF8Encoder->encode(-43.23));

        // booleans
        $this->assertEquals(true, $recursiveUTF8Encoder->encode(true));
        $this->assertEquals(false, $recursiveUTF8Encoder->encode(false));

        // array
        $original = array('foo', utf8_decode('Hähnchen'), 'bar');
        $encoded  = array('foo', 'Hähnchen', 'bar');
        $this->assertEquals($encoded, $recursiveUTF8Encoder->encode($original));

        // object
        $original = new stdClass();
        $original->foo = utf8_decode('Hähnchen');
        $encoded = new stdClass();
        $encoded->foo = 'Hähnchen';
        $this->assertEquals($encoded, $recursiveUTF8Encoder->encode($original));

        // mixed
        $original       = new stdClass();
        $original->foo  = utf8_decode('Hähnchen');
        $originalObject = array(utf8_decode('Hähnchen'), $original);

        $encoded       = new stdClass();
        $encoded->foo  = 'Hähnchen';
        $encodedObject = array('Hähnchen', $encoded);
        $this->assertEquals($encodedObject, $recursiveUTF8Encoder->encode($originalObject));
    }
}
?>