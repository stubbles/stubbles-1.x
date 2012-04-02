<?php
/**
 * Test for net::stubbles::reflection::stubReflectionProperty.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionPropertyTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionProperty');
/**
 * class for testing purposes
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubTestProperty1
{
    public $property;
    private $anotherProperty;
}
/**
 * another class for testing purposes
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubTestProperty2 extends stubTestProperty1 { }
/**
 * Test for net::stubbles::reflection::stubReflectionProperty.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionPropertyTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubReflectionProperty
     */
    protected $stubRefProperty;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->stubRefProperty = new stubReflectionProperty('stubTestProperty1', 'property');
    }

    /**
     * assure that instances of stubReflectionExtension for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefProperty->equals($this->stubRefProperty));
        $stubRefProperty1 = new stubReflectionProperty('stubTestProperty1', 'property');
        $stubRefProperty2 = new stubReflectionProperty('stubTestProperty1', 'anotherProperty');
        $this->assertTrue($this->stubRefProperty->equals($stubRefProperty1));
        $this->assertTrue($stubRefProperty1->equals($this->stubRefProperty));
        $this->assertFalse($this->stubRefProperty->equals($stubRefProperty2));
        $this->assertFalse($this->stubRefProperty->equals('foo'));
        $this->assertFalse($stubRefProperty2->equals($this->stubRefProperty));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionProperty[stubTestProperty1::property] {\n}\n", (string) $this->stubRefProperty);
    }

    /**
     * test that getting the functions works correct
     *
     * @test
     */
    public function getDeclaringClass()
    {
        $declaringClass = $this->stubRefProperty->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $declaringClass);
        $this->assertEquals('stubTestProperty1', $declaringClass->getName());
        
        $refClass1 = new stubReflectionClass('stubTestProperty1');
        $stubRefProperty1 = new stubReflectionProperty($refClass1, 'property');
        $this->assertSame($refClass1, $stubRefProperty1->getDeclaringClass());
        
        $refClass2 = new stubReflectionClass('stubTestProperty2');
        $stubRefProperty2 = new stubReflectionProperty($refClass2, 'property');
        $this->assertEquals('stubTestProperty1', $stubRefProperty2->getDeclaringClass()->getName());        
    }
}
?>