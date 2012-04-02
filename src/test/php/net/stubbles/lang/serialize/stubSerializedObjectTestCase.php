<?php
/**
 * Tests for net::stubbles::lang::serialize::stubSerializedObject.
 *
 * @package     stubbles
 * @subpackage  lang_serialize_test
 * @version     $Id: stubSerializedObjectTestCase.php 3273 2011-12-09 15:07:44Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::serialize::stubSerializedObject');
class stub3stubSerializableObject extends stubSerializableObject
{
    protected $bar;
    
    public function getClassname()
    {
        return 'test::stub3stubSerializableObject';
    }
    
    public function setBar($bar)
    {
        $this->bar = $bar;
    }
    
    public function getBar()
    {
        return $this->bar;
    }
}
/**
 * Tests for net::stubbles::lang::serialize::stubSerializedObject.
 *
 * @package     stubbles
 * @subpackage  lang_serialize_test
 * @group       lang
 * @group       lang_serialize
 */
class stubSerializedObjectTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @var  stubSerializableObject
     */
    protected $stubSerializableObject;
    /**
     * instance to test
     *
     * @var  stubSerializedObject
     */
    protected $serializedObject;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stubSerializableObject   = new stub3stubSerializableObject();
        $this->stubSerializableObject->setBar(313);
        $this->serializedObject = new stubSerializedObject($this->stubSerializableObject);
    }

    /**
     * assert that default values are as expected
     *
     * @test
     */
    public function values()
    {
        $this->assertEquals('test::stub3stubSerializableObject', $this->serializedObject->getSerializedClassName());
        $this->assertEquals('serialized: ' . $this->stubSerializableObject->hashCode(), $this->serializedObject->hashCode());
    }

    /**
     * assure that unserialize works as expected
     *
     * @test
     */
    public function unserialize()
    {
        $restored = $this->serializedObject->getUnserialized();
        $this->assertInstanceOf('stub3stubSerializableObject', $restored);
        $this->assertEquals(313, $restored->getBar());
    }

    /**
     * assure that class name mapping works as expected
     *
     * @test
     */
    public function getClass()
    {
        $refObject = $this->serializedObject->getClass();
        $this->assertInstanceOf('stubReflectionObject', $refObject);
        $this->assertEquals('stubSerializedObject', $refObject->getName());
    }

    /**
     * correct package should be returned
     *
     * @test
     */
    public function packageAndPackageName()
    {
        $refPackage = $this->serializedObject->getPackage();
        $this->assertInstanceOf('stubReflectionPackage', $refPackage);
        $this->assertEquals('net::stubbles::lang::serialize', $refPackage->getName());
        $this->assertEquals('net::stubbles::lang::serialize', $this->serializedObject->getPackageName());
    }

    /**
     * assure that the equal() method works correct
     *
     * @test
     */
    public function comparisonwithEquals()
    {
        $this->assertTrue($this->serializedObject->equals($this->serializedObject));
        $this->assertTrue($this->serializedObject->equals(new stubSerializedObject($this->stubSerializableObject)));
        $this->assertFalse($this->serializedObject->equals($this->stubSerializableObject));
        $this->assertFalse($this->serializedObject->equals('foo'));
        $this->assertFalse($this->serializedObject->equals(6));
        $this->assertFalse($this->serializedObject->equals(true));
        $this->assertFalse($this->serializedObject->equals(false));
        $this->assertFalse($this->serializedObject->equals(null));
        $this->assertFalse($this->serializedObject->equals(new stubSerializedObject(new stub3stubSerializableObject())));
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function getSerializedThrowsRuntimeException()
    {
        $this->serializedObject->getSerialized();
    }

    /**
     * assure that the __toString() method works correct
     *
     * @test
     */
    public function toStringCreatesClassRepresentation()
    {
        $this->assertEquals("net::stubbles::lang::serialize::stubSerializedObject {\n    fqClassName(string): test::stub3stubSerializableObject\n    nqClassName(string): stub3stubSerializableObject\n    data(string): " . serialize($this->stubSerializableObject) . "\n}\n", (string) $this->serializedObject);
    }

    /**
     * assure that changes to the serialized object are not thrown away
     *
     * @test
     */
    public function dataIntegrity()
    {
        $stub3 = $this->serializedObject->getUnserialized();
        $this->assertSame($this->stubSerializableObject, $stub3);
        $this->stubSerializableObject->setBar(303);
        
        // next request
        $newSerializedObject = unserialize(serialize($this->serializedObject));
        $stub3b = $newSerializedObject->getUnserialized();
        $stub3c = $newSerializedObject->getUnserialized();
        $this->assertSame($stub3b, $stub3c);
        $this->assertEquals(303, $stub3b->getBar());
        $stub3b->setBar(909);
        
        // next request
        $moreSerializedObject = unserialize(serialize($this->serializedObject));
        $stub3d = $newSerializedObject->getUnserialized();
        $this->assertEquals(909, $stub3d->getBar());
    }

    /**
     * test against bug: ensure that the integrity of the serialized object
     * is maintained over several serialize/unserialize operations
     *
     * @test
     */
    public function serializationIntegrity()
    {
        $this->stubSerializableObject->setBar(808);
        $foo = serialize($this->serializedObject);
        $bar = unserialize($foo);
        $baz = serialize($bar);
        $test = unserialize($baz);
        $serializableObject = $test->getUnserialized();
        $this->assertInstanceOf('stub3stubSerializableObject', $serializableObject);
        $this->assertEquals(808, $serializableObject->getBar());
    }
}
?>