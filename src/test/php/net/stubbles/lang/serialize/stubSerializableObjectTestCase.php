<?php
/**
 * Tests for net::stubbles::lang::serialize::stubSerializableObject.
 *
 * @package     stubbles
 * @subpackage  lang_serialize_test
 * @version     $Id: stubSerializableObjectTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::serialize::stubSerializableObject');
class stub1stubSerializableObject extends stubSerializableObject
{
    protected $bar = 5;
    
    public function getClassname()
    {
        return 'test::stub1stubSerializableObject';
    }
}
class stub2stubSerializableObject extends stubSerializableObject
{
    public $stubSerializableObject;
    
    private $foo = 'bar';
    
    public function getClassname()
    {
        return 'test::stub2stubSerializableObject';
    }
    
    public function getSerializedProperties()
    {
        return $this->_serializedProperties;
    }
}
/**
 * Tests for net::stubbles::lang::serialize::stubSerializableObject.
 *
 * @package     stubbles
 * @subpackage  lang_serialize_test
 * @group       lang
 * @group       lang_serialize
 */
class stubSerializableObjectTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @var  stubSerializableObject
     */
    protected $stubSerializableObject1;
    /**
     * instance 2 to be used for tests
     *
     * @var  stubSerializableObject
     */
    protected $stubSerializableObject2;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stubSerializableObject1 = new stub1stubSerializableObject();
        $this->stubSerializableObject2 = new stub2stubSerializableObject();
        $this->stubSerializableObject2->stubSerializableObject = $this->stubSerializableObject1;
    }

    /**
     * assure that serialization delivers the correct class
     *
     * @test
     */
    public function getSerialized()
    {
        $serialized = $this->stubSerializableObject1->getSerialized();
        $this->assertInstanceOf('stubSerializedObject', $serialized);
    }

    /**
     * assure that the __toString() method works correct
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("test::stub1stubSerializableObject {\n    bar(integer): 5\n}\n", (string) $this->stubSerializableObject1);
        $this->assertEquals("test::stub2stubSerializableObject {\n    stubSerializableObject(test::stub1stubSerializableObject): test::stub1stubSerializableObject {\n        bar(integer): 5\n    }\n    foo(string): bar\n}\n", (string) $this->stubSerializableObject2);
    }

    /**
     * assure that the __sleep() method works correct
     *
     * @test
     */
    public function sleep()
    {
        $this->assertEquals(array('foo', '_serializedProperties'), $this->stubSerializableObject2->__sleep());
        $serializedProperties = $this->stubSerializableObject2->getSerializedProperties();
        $this->assertTrue(isset($serializedProperties['stubSerializableObject']));
        $this->assertInstanceOf('stubSerializedObject', $serializedProperties['stubSerializableObject']);
        $this->assertEquals('test::stub1stubSerializableObject', $serializedProperties['stubSerializableObject']->getSerializedClassName());
    }
}
?>