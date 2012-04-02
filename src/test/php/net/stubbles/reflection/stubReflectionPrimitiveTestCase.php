<?php
/**
 * Test for net::stubbles::reflection::stubReflectionPrimitive.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  reflection_test
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionPrimitive');
/**
 * Test for net::stubbles::reflection::stubReflectionPrimitive.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionPrimitiveTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * helper instance for enum
     *
     * @var  ReflectionClass
     */
    protected $refClass;
 
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->refClass = new ReflectionClass('stubReflectionPrimitive');
    }

    /**
     * assure that string instance works as expected
     *
     * @test
     */
    public function stringPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'string');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('string', $primitive->name());
        $this->assertEquals('string', $primitive->value());
        $this->assertEquals('string', $primitive->getName());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[string] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
    }
 
    /**
     * assure that int and integer instance works as expected
     *
     * @test
     */
    public function intPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'int');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('int', $primitive->name());
        $this->assertEquals('int', $primitive->getName());
        $this->assertEquals('int', $primitive->value());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[int] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
        
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'integer');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('integer', $primitive->name());
        $this->assertEquals('integer', $primitive->getName());
        $this->assertEquals('int', $primitive->value());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[int] {\n}\n", (string) $primitive);
    }

    /**
     * assure that float instance works as expected
     *
     * @test
     */
    public function floatPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'float');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('float', $primitive->name());
        $this->assertEquals('float', $primitive->getName());
        $this->assertEquals('float', $primitive->value());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[float] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
    }

    /**
     * assure that double instance works as expected
     *
     * @test
     */
    public function doublePrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'double');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('double', $primitive->name());
        $this->assertEquals('double', $primitive->getName());
        $this->assertEquals('float', $primitive->value());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[float] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
    }

    /**
     * assure that bool and boolean instance works as expected
     *
     * @test
     */
    public function boolPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'bool');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('bool', $primitive->name());
        $this->assertEquals('bool', $primitive->getName());
        $this->assertEquals('bool', $primitive->value());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[bool] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
        
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'boolean');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('boolean', $primitive->name());
        $this->assertEquals('boolean', $primitive->getName());
        $this->assertEquals('bool', $primitive->value());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[bool] {\n}\n", (string) $primitive);
    }

    /**
     * assure that array instance works as expected
     *
     * @test
     */
    public function arrayPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'array');
        $this->assertInstanceOf('stubReflectionPrimitive', $primitive);
        $this->assertEquals('array', $primitive->name());
        $this->assertEquals('array', $primitive->getName());
        $this->assertEquals('array', $primitive->value());
        $this->assertFalse($primitive->isObject());
        $this->assertTrue($primitive->isPrimitive());
        $this->assertEquals("net::stubbles::reflection::stubReflectionPrimitive[array] {\n}\n", (string) $primitive);
        $this->assertFalse($primitive->equals(new stdClass()));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'string')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'int')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'integer')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'float')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'double')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'bool')));
        $this->assertFalse($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'boolean')));
        $this->assertTrue($primitive->equals(stubReflectionPrimitive::forName($this->refClass, 'array')));
        
        $primitive2 = stubReflectionPrimitive::forName($this->refClass, 'array<string,stdClass>');
        $this->assertSame($primitive, $primitive2);
    }

    /**
     * assert that a non-primitive instance will not be created
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function nonPrimitive()
    {
        $primitive = stubReflectionPrimitive::forName($this->refClass, 'stdClass');
    }
}
?>