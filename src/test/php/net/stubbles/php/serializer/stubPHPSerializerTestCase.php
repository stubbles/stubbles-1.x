<?php
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializer.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @version     $Id: stubPHPSerializerTestCase.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::php::serializer::stubPHPSerializer');
interface TestSerializedMapping0 { }
class TestSerializedMapping1 implements TestSerializedMapping0 { }
class TestSerializedMapping2 extends TestSerializedMapping1 { }
class TestSerializedMapping4 extends TestSerializedMapping2 { }
class TestSerializedMapping5 extends TestSerializedMapping4 { }
/**
 * Tests for net::stubbles::php::serializer::stubPHPSerializer.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 * @group       php
 * @group       php_serializer
 */
class stubPHPSerializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPHPSerializer
     */
    protected $serializer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->serializer = new stubPHPSerializer();
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeNull()
    {
        $this->assertEquals(serialize(null), $this->serializer->serialize(null));
        $this->assertEquals(unserialize(serialize(null)), $this->serializer->unserialize(new stubPHPSerializedData('N;')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeBoolean()
    {
        $this->assertEquals(serialize(true), $this->serializer->serialize(true));
        $this->assertEquals(unserialize(serialize(true)), $this->serializer->unserialize(new stubPHPSerializedData('b:1;')));
        $this->assertEquals(serialize(false), $this->serializer->serialize(false));
        $this->assertEquals(unserialize(serialize(false)), $this->serializer->unserialize(new stubPHPSerializedData('b:0;')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeInteger()
    {
        $this->assertEquals(serialize(313), $this->serializer->serialize(313));
        $this->assertEquals(unserialize(serialize(313)), $this->serializer->unserialize(new stubPHPSerializedData('i:313;')));
        $this->assertEquals(serialize(-313), $this->serializer->serialize(-313));
        $this->assertEquals(unserialize(serialize(-313)), $this->serializer->unserialize(new stubPHPSerializedData('i:-313;')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeDouble()
    {
        // can not compare serialized strings because php has rounding problems when serializing doubles
        //$this->assertEquals(serialize(3.03), $this->serializer->serialize(3.03));
        $this->assertEquals(unserialize(serialize(3.03)), $this->serializer->unserialize(new stubPHPSerializedData('d:3.03;')));
        //$this->assertEquals(serialize(-3.03), $this->serializer->serialize(-3.03));
        $this->assertEquals(unserialize(serialize(-3.03)), $this->serializer->unserialize(new stubPHPSerializedData('d:-3.03;')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeString()
    {
        $this->assertEquals(serialize('foo'), $this->serializer->serialize('foo'));
        $this->assertEquals(unserialize(serialize('foo')), $this->serializer->unserialize(new stubPHPSerializedData('s:3:"foo";')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeNumericArray()
    {
        $this->assertEquals(serialize(array('foo', 'bar')), $this->serializer->serialize(array('foo', 'bar')));
        $this->assertEquals(unserialize(serialize(array('foo', 'bar'))), $this->serializer->unserialize(new stubPHPSerializedData('a:2:{i:0;s:3:"foo";i:1;s:3:"bar";}')));
    }

    /**
     * test that serializing works the same as in native PHP
     *
     * @test
     */
    public function serializeAssocArray()
    {
        $this->assertEquals(serialize(array('foo' => 'bar')), $this->serializer->serialize(array('foo' => 'bar')));
        $this->assertEquals(unserialize(serialize(array('foo' => 'bar'))), $this->serializer->unserialize(new stubPHPSerializedData('a:1:{s:3:"foo";s:3:"bar";}')));
    }

    /**
     * assert that serializing using a mapping works as expected
     *
     * @test
     */
    public function serializeWithMapping()
    {
        $mockMapping = $this->getMock('stubPHPSerializerMapping');
        $mockMapping->expects($this->any())->method('getToken')->will($this->returnValue('X'));
        $mockMapping->expects($this->once())->method('getHandledClass')->will($this->returnValue(new ReflectionClass('TestSerializedMapping1')));
        $test    = new TestSerializedMapping1();
        $context = array('foo' => 'bar');
        $mockMapping->expects($this->exactly(2))
                    ->method('serialize')
                    ->with($this->anything(), $this->equalTo($test), $this->equalTo($context))
                    ->will($this->returnValue('X:dummy'));
        $this->serializer->addMapping($mockMapping);
        $this->assertEquals('X:dummy', $this->serializer->serialize($test, $context));
        $this->assertEquals('X:dummy', $this->serializer->serialize($test, $context));
    }

    /**
     * assert that an exception is thrown on unknown mappings
     *
     * @test
     * @expectedException  stubFormatException
     */
    public function unserializeUnknownMapping()
    {
        $this->serializer->unserialize(new stubPHPSerializedData('X:dummy'));
    }

    /**
     * assert that mapping will be used correctly
     *
     * @test
     */
    public function unserializeKnownMapping()
    {
        $mockMapping = $this->getMock('stubPHPSerializerMapping');
        $mockMapping->expects($this->any())->method('getToken')->will($this->returnValue('X'));
        $data    = new stubPHPSerializedData('X:dummy');
        $context = array('foo' => 'bar');
        $mockMapping->expects($this->any())
                    ->method('unserialize')
                    ->with($this->anything(), $this->equalTo($data), $this->equalTo($context))
                    ->will($this->returnValue('dummy'));
        $this->serializer->addMapping($mockMapping);
        $this->assertEquals('dummy', $this->serializer->unserialize($data, $context));
    }

    /**
     * test that always the best mapping will be selected
     *
     * @test
     */
    public function bestMapping()
    {
        $mockMapping0 = $this->getMock('stubPHPSerializerMapping');
        $mockMapping0->expects($this->any())->method('getToken')->will($this->returnValue('FOO'));
        $mockMapping0->expects($this->any())->method('getHandledClass')->will($this->returnValue(new ReflectionClass('TestSerializedMapping0')));
        $mockMapping0->expects($this->any())->method('serialize')->will($this->returnValue('FOO:dummy'));
        $mockMapping1 = $this->getMock('stubPHPSerializerMapping');
        $mockMapping1->expects($this->any())->method('getToken')->will($this->returnValue('BAR'));
        $mockMapping1->expects($this->any())->method('getHandledClass')->will($this->returnValue(new ReflectionClass('TestSerializedMapping1')));
        $mockMapping1->expects($this->any())->method('serialize')->will($this->returnValue('BAR:dummy'));
        $mockMapping2 = $this->getMock('stubPHPSerializerMapping');
        $mockMapping2->expects($this->any())->method('getToken')->will($this->returnValue('BAZ'));
        $mockMapping2->expects($this->any())->method('getHandledClass')->will($this->returnValue(new ReflectionClass('TestSerializedMapping2')));
        $mockMapping2->expects($this->any())->method('serialize')->will($this->returnValue('BAZ:dummy'));
        
        // both must be serialized with the FOO mapping, because both are
        // instances of TestSerializedMapping0
        $this->serializer->addMapping($mockMapping0);
        $this->assertEquals('FOO:dummy', $this->serializer->serialize(new TestSerializedMapping1()));
        $this->assertEquals('FOO:dummy', $this->serializer->serialize(new TestSerializedMapping2()));
        $this->assertEquals('FOO:dummy', $this->serializer->serialize(new TestSerializedMapping4()));
        $this->assertEquals('FOO:dummy', $this->serializer->serialize(new TestSerializedMapping5()));
        
        // both must be serialized with the BAR mapping, because both are
        // TestSerializedMapping1 or TestSerializedMapping1-derived objects.
        $this->serializer->addMapping($mockMapping1);
        $this->assertEquals('BAR:dummy', $this->serializer->serialize(new TestSerializedMapping1()));
        $this->assertEquals('BAR:dummy', $this->serializer->serialize(new TestSerializedMapping2()));
        $this->assertEquals('BAR:dummy', $this->serializer->serialize(new TestSerializedMapping4()));
        $this->assertEquals('BAR:dummy', $this->serializer->serialize(new TestSerializedMapping5()));
        
        // add more concrete mapping for BAZ. TestSerializedMapping1 must still
        // be serialized with BAR, but the TestSerializedMapping2-object has a
        // better matching mapping.
        $this->serializer->addMapping($mockMapping2);
        $this->assertEquals('BAR:dummy', $this->serializer->serialize(new TestSerializedMapping1()));
        $this->assertEquals('BAZ:dummy', $this->serializer->serialize(new TestSerializedMapping2()));
        $this->assertEquals('BAZ:dummy', $this->serializer->serialize(new TestSerializedMapping4()));
        $this->assertEquals('BAZ:dummy', $this->serializer->serialize(new TestSerializedMapping5()));
    }

    /**
     * test mapping of packages
     *
     * @test
     */
    public function packageMapping()
    {
        $this->assertEquals('remote::lang::type', $this->serializer->translateToLocalePackage('remote::lang::type'));
        $this->serializer->addPackageMapping('types', 'type');
        $this->assertEquals('remote::lang::types', $this->serializer->translateToLocalePackage('remote::lang::type'));
        $this->serializer->addPackageMapping('net::stubbles', 'remote');
        $this->assertEquals('net::stubbles::lang::types', $this->serializer->translateToLocalePackage('remote::lang::type'));
    }

    /**
     * test that exceptions are correctly mapped
     *
     * @test
     */
    public function exceptionMapping()
    {
        $this->serializer->addExceptionMapping('origin::Exception', 'target.Exception');
        $this->serializer->addExceptionMapping('old::Exception', 'new.Exception');
        $this->assertEquals('origin::Exception', $this->serializer->getLocalException('target.Exception'));
        $this->assertEquals('old::Exception', $this->serializer->getLocalException('new.Exception'));
        $this->assertEquals('net::stubbles::php::serializer::stubExceptionReference', $this->serializer->getLocalException('another.Exception'));
        $this->assertEquals('target.Exception', $this->serializer->getRemoteException('origin::Exception'));
        $this->assertEquals('new.Exception', $this->serializer->getRemoteException('old::Exception'));
        $this->assertNull($this->serializer->getRemoteException('another::Exception'));
    }
}
?>