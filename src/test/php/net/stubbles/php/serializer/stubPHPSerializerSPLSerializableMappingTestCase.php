<?php
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializerSPLSerializableMapping.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @version     $Id: stubPHPSerializerSPLSerializableMappingTestCase.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::php::serializer::stubPHPSerializer',
                      'net::stubbles::php::serializer::stubPHPSerializerSPLSerializableMapping'
);
class TestForSPLSerializable implements Serializable
{
    protected $dummy;

    public function __construct()
    {
        $this->dummy = 'dummy';
    }

    public function getDummy()
    {
        return $this->dummy;
    }

    public function serialize()
    {
        return $this->dummy;
    }
    
    public function unserialize($data)
    {
        $this->dummy = $data;
    }
}
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializerSPLSerializableMapping.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 * @group       php
 * @group       php_serializer
 */
class stubPHPSerializerSPLSerializableMappingTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance is required for the mapping
     *
     * @var  stubPHPSerializer
     */
    protected $serializer;
    /**
     * instance to test
     *
     * @var  stubPHPSerializerSPLSerializableMapping
     */
    protected $serializableMapping;
    /**
     * instance to serialize
     *
     * @var  TestForSPLSerializable
     */
    protected $serializable;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->serializer          = new stubPHPSerializer();
        $this->serializableMapping = new stubPHPSerializerSPLSerializableMapping();
        $this->serializable        = new TestForSPLSerializable();
    }

    /**
     * test that serializing a non-instance of Serializable triggers an exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function noSerializable()
    {
        $this->serializableMapping->serialize($this->serializer, new stdClass());
    }

    /**
     * assure that both serializes deliver the same result
     *
     * @test
     */
    public function serialize()
    {
        $this->assertEquals(serialize($this->serializable), $this->serializableMapping->serialize($this->serializer, $this->serializable));
    }

    /**
     * assure that both unserializes deliver the same result
     *
     * @test
     */
    public function unserialize()
    {
        $unserialized = $this->serializableMapping->unserialize($this->serializer, new stubPHPSerializedData(serialize($this->serializable)));
        $this->assertEquals(unserialize(serialize($this->serializable)), $unserialized);
        $this->assertEquals('dummy', $unserialized->getDummy());
    }

    /**
     * assure that corrupt data throws an exception
     *
     * @test
     * @expectedException  stubFormatException
     */
    public function unserializeCorruptData()
    {
        $this->serializableMapping->unserialize($this->serializer, new stubPHPSerializedData('foo'));
    }
}
?>