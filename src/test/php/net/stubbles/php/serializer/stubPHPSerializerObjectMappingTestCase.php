<?php
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializerObjectMapping.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @version     $Id: stubPHPSerializerObjectMappingTestCase.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::php::serializer::stubPHPSerializer',
                      'net::stubbles::php::serializer::stubPHPSerializerObjectMapping'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 */
class TestSerializingWithSleepAndWakeup
{
    public $foo1         = 'foo1';
    protected $foo2      = 'foo2';
    public $bar          = 'bar';
    public $wakeupCalled = false;
    public function __sleep() { return array('foo1', 'foo2'); }
    public function __wakeup() { $this->wakeupCalled = true; }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 */
class TestForSerializingObjects extends stubBaseObject
{
    public $foo    = 'foo';
    protected $bar = '';
    private  $baz  = 'baz';
    public function setBar() { $this->bar = 'bar'; }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 */
class TestSerializingPropertyClass { public $blub = 'foo'; }
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 */
class TestSerializingWithSetState extends stubBaseObject
{
    public $foo;
    protected $bar = 'bar';
    private  $baz  = 'baz';
    public function __construct() { $this->foo = new TestSerializingPropertyClass(); }
    public function setBar() { $this->bar = 'bar2'; }
    public function getBar() { return $this->bar; }
    public function setBaz() { $this->baz = 'baz2'; }
    public function getBaz() { return $this->baz; }
    public static function __set_state($properties)
    {
        $instance = new self();
        foreach ($properties as $propertyName => $propertyValue) {
            if (isset($instance->$propertyName) == true) {
                $instance->$propertyName = $propertyValue;
            }
        }
        
        return $instance;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 */
class TestPHPSerializerMapping extends stubBaseObject implements stubPHPSerializerMapping
{
    public $return;
    public function getToken() { return 'X'; }
    public function getHandledClass() { return new ReflectionClass('TestSerializingPropertyClass'); }
    public function serialize(stubPHPSerializer $serializer, $object, array $context = array())
    {
        return 'X:dummy';
    }
    public function unserialize(stubPHPSerializer $serializer, stubPHPSerializedData $serialized, array $context = array())
    {
        $serialized->moveOffset(7);
        return $this->return;
    }
}
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializerObjectMapping.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 * @group       php
 * @group       php_serializer
 */
class stubPHPSerializerObjectMappingTestCase extends PHPUnit_Framework_TestCase
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
     * @var  stubPHPSerializerObjectMapping
     */
    protected $objectMapping;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->serializer    = new stubPHPSerializer();
        $this->objectMapping = new stubPHPSerializerObjectMapping();
    }

    /**
     * token for this mapping is always "O"
     *
     * @test
     */
    public function tokenIsAlwaysO()
    {
        $this->assertEquals('O', $this->objectMapping->getToken());
    }

    /**
     * returned information about handled class is always the same
     *
     * @test
     */
    public function handledClassIsAlwaysTheSame()
    {
        $refClass = $this->objectMapping->getHandledClass();
        $this->assertInstanceOf('ReflectionClass', $refClass);
        $this->assertSame($refClass, $this->objectMapping->getHandledClass());
    }

    /**
     * test that serializing a non-object triggers an exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function noObject()
    {
        $this->objectMapping->serialize($this->serializer, 'blub');
    }

    /**
     * test that unserializing an object for which the class has not been loaded
     * results in an instance of net::stubbles::php::serializer::stubUnknownObject
     *
     * @test
     */
    public function unserializeUnknownObjectWithoutProperties()
    {
        $unknownObject = $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData('O:22:"TestUnknownObjectClass":0:{}'));
        $this->assertInstanceOf('stubUnknownObject', $unknownObject);
        $this->assertEquals('TestUnknownObjectClass', $unknownObject->getName());
        $this->assertEquals(array(), $unknownObject->getProperties());
    }

    /**
     * test that unserializing an object for which the class has not been loaded
     * results in an instance of net::stubbles::php::serializer::stubUnknownObject
     *
     * @test
     */
    public function unserializeUnknownObjectWithProperties()
    {
        $unknownObject = $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData('O:22:"TestUnknownObjectClass":1:{s:3:"bar";s:3:"baz";}'));
        $this->assertInstanceOf('stubUnknownObject', $unknownObject);
        $this->assertEquals('TestUnknownObjectClass', $unknownObject->getName());
        $this->assertEquals(array('bar' => 'baz'), $unknownObject->getProperties());
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeSimpleObject()
    {
        $foo      = new stdClass();
        $foo->bar = 'baz';
        $this->assertEquals(serialize($foo), $this->objectMapping->serialize($this->serializer, $foo));
        $this->assertEquals(unserialize(serialize($foo)), $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData('O:8:"stdClass":1:{s:3:"bar";s:3:"baz";}')));
    }

    /**
     * assert that __sleep() and __wakeup() are called correct
     *
     * @test
     */
    public function serializeObjectWithSleepAndWakeup()
    {
        $test = new TestSerializingWithSleepAndWakeup();
        $serialized = $this->objectMapping->serialize($this->serializer, $test);
        $this->assertEquals(serialize($test), $serialized);
        $this->assertEquals('O:33:"TestSerializingWithSleepAndWakeup":2:{s:4:"foo1";s:4:"foo1";s:7:"' . "\0*\0" . 'foo2";s:4:"foo2";}', $serialized);
        $test->bar  = 'baz';
        $testResult = $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData(serialize($test)));
        $this->assertEquals(unserialize(serialize($test)), $testResult);
        $this->assertEquals('bar', $testResult->bar);
        $this->assertTrue($testResult->wakeupCalled);
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeStubObject()
    {
        $foo = new TestForSerializingObjects();
        $foo->setBar();
        $this->assertEquals(serialize($foo), $this->objectMapping->serialize($this->serializer, $foo));
        $this->assertEquals(unserialize(serialize($foo)), $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData('O:25:"TestForSerializingObjects":3:{s:3:"foo";s:3:"foo";s:6:"' . "\0" . '*' . "\0" . 'bar";s:3:"bar";s:30:"' . "\0" . 'TestForSerializingObjects' . "\0" . 'baz";s:3:"baz";}')));
    }

    /**
     * test unserializing a structure that contains a mapping that is not known
     *
     * @test
     * @expectedException  stubFormatException
     */
    public function unserializeWithUnknownMappingAndNoSetStateMethod()
    {
        $mockMapping = $this->getMock('stubPHPSerializerMapping');
        $mockMapping->expects($this->any())->method('getToken')->will($this->returnValue('X'));
        $mockMapping->expects($this->any())->method('unserialize')->will($this->returnValue('dummy'));
        $this->serializer->addMapping($mockMapping);
        $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData('O:8:"stdClass":1:{X:dummy;}'));
    }

    /**
     * test unserializing a structure that contains a mapping that is not known
     *
     * @test
     */
    public function unserializeWithKnownMappingAndSetStateMethod()
    {
        $mockMapping = new TestPHPSerializerMapping();
        $mockMapping->return = new TestSerializingPropertyClass();
        $mockMapping->return->blub = 'foo2';
        $this->serializer->addMapping($mockMapping);
        $test = new TestSerializingWithSetState();
        $test->setBar();
        $test->setBaz();
        $serialized = $this->objectMapping->serialize($this->serializer, $test);
        $unserialized = $this->objectMapping->unserialize($this->serializer, new stubPHPSerializedData($serialized));
        $this->assertInstanceOf('TestSerializingWithSetState', $unserialized);
        $this->assertSame($unserialized->foo, $mockMapping->return);
        $this->assertEquals('bar2', $unserialized->getBar());
        $this->assertEquals('baz2', $unserialized->getBaz());
    }
}
?>