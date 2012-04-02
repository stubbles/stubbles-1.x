<?php
/**
 * Test for net::stubbles::reflection::stubReflectionClass.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  reflection_test
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionClass');
require_once dirname(__FILE__) . '/stubreflectiontestclasses.php';
class stubTestFullLoading {}
/**
 * Test for net::stubbles::reflection::stubReflectionClass.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionClassTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @var  stubReflectionClass
     */
    protected $stubRefClass1;
    /**
     * instance 2 to test
     *
     * @var  stubReflectionClass
     */
    protected $stubRefClass2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->stubRefClass1 = new stubReflectionClass('stubTestWithMethodsAndProperties');
        $this->stubRefClass2 = new stubReflectionClass('stubTestWithOutMethodsAndProperties');
    }

    /**
     * assure that instances of stubReflectionClass for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefClass1->equals($this->stubRefClass1));
        $this->assertTrue($this->stubRefClass2->equals($this->stubRefClass2));
        $stubRefClass = new stubReflectionClass('stubTestWithMethodsAndProperties');
        $this->assertTrue($this->stubRefClass1->equals($stubRefClass));
        $this->assertTrue($stubRefClass->equals($this->stubRefClass1));
        $this->assertFalse($this->stubRefClass1->equals($this->stubRefClass2));
        $this->assertFalse($this->stubRefClass1->equals('foo'));
        $this->assertFalse($this->stubRefClass2->equals($this->stubRefClass1));
        $this->assertFalse($this->stubRefClass2->equals($stubRefClass));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionClass[stubTestWithMethodsAndProperties] {\n}\n", (string) $this->stubRefClass1);
        $this->assertEquals("net::stubbles::reflection::stubReflectionClass[stubTestWithOutMethodsAndProperties] {\n}\n", (string) $this->stubRefClass2);
    }

    /**
     * test the full qualified class name
     *
     * @test
     */
    public function getFullQualifiedClassName()
    {
        $this->assertEquals('stubTestWithMethodsAndProperties', $this->stubRefClass1->getFullQualifiedClassName());
        $this->assertEquals('stubTestWithOutMethodsAndProperties', $this->stubRefClass2->getFullQualifiedClassName());
    }

    /**
     * test that getting the constructor method works correct
     *
     * @test
     */
    public function getConstructor()
    {
        $stubRefMethod = $this->stubRefClass1->getConstructor();
        $this->assertInstanceOf('stubReflectionMethod', $stubRefMethod);
        $this->assertEquals('__construct', $stubRefMethod->getName());
        
        $this->assertNull($this->stubRefClass2->getConstructor());
    }

    /**
     * test that getting the specified method works correct
     *
     * @test
     */
    public function getMethod()
    {
        $stubRefMethod = $this->stubRefClass1->getMethod('methodA');
        $this->assertInstanceOf('stubReflectionMethod', $stubRefMethod);
        $this->assertEquals('methodA', $stubRefMethod->getName());
        
        $stubRefMethod = $this->stubRefClass1->getMethod('doesNotExist');
        $this->assertNull($stubRefMethod);
    }

    /**
     * test that getting the methods works correct
     *
     * @test
     */
    public function getMethods()
    {
        $stubRefMethods = $this->stubRefClass1->getMethods();
        $this->assertEquals(4, count($stubRefMethods));
        foreach ($stubRefMethods as $stubRefMethod) {
            $this->assertInstanceOf('stubReflectionMethod', $stubRefMethod);
        }
        
        $stubRefMethods = $this->stubRefClass2->getMethods();
        $this->assertEquals(0, count($stubRefMethods));
    }

    /**
     * test that getting the methods works correct
     *
     * @test
     */
    public function getMethodsByMatcher()
    {
        $mockMethodMatcher = $this->getMock('stubMethodMatcher');
        $mockMethodMatcher->expects($this->exactly(4))
                          ->method('matchesMethod')
                          ->will($this->onConsecutiveCalls(true, true, false, false));
        $mockMethodMatcher->expects($this->exactly(2))
                          ->method('matchesAnnotatableMethod')
                          ->will($this->onConsecutiveCalls(false, true));
        $stubRefMethods = $this->stubRefClass1->getMethodsByMatcher($mockMethodMatcher);
        $this->assertEquals(1, count($stubRefMethods));
        $this->assertInstanceOf('stubReflectionMethod', $stubRefMethods[0]);
    }

    /**
     * test that getting the specified property works correct
     *
     * @test
     */
    public function getProperty()
    {
        $stubRefProperty = $this->stubRefClass1->getProperty('property1');
        $this->assertInstanceOf('stubReflectionProperty', $stubRefProperty);
        $this->assertEquals('property1', $stubRefProperty->getName());
        
        $stubRefProperty = $this->stubRefClass1->getProperty('doesNotExist');
        $this->assertNull($stubRefProperty);
    }

    /**
     * test that getting the properties works as expected
     *
     * @test
     */
    public function getProperties()
    {
        $stubRefProperties = $this->stubRefClass1->getProperties();
        $this->assertEquals(3, count($stubRefProperties));
        foreach ($stubRefProperties as $stubRefProperty) {
            $this->assertInstanceOf('stubReflectionProperty', $stubRefProperty);
        }
        
        $stubRefProperties = $this->stubRefClass2->getProperties();
        $this->assertEquals(0, count($stubRefProperties));
    }

    /**
     * test that getting the methods works correct
     *
     * @test
     */
    public function getPropertiesByMatcher()
    {
        $mockPropertyMatcher = $this->getMock('stubPropertyMatcher');
        $mockPropertyMatcher->expects($this->exactly(3))
                            ->method('matchesProperty')
                            ->will($this->onConsecutiveCalls(true, false, false));
        $mockPropertyMatcher->expects($this->once())
                            ->method('matchesAnnotatableProperty')
                            ->will($this->returnValue(true));
        $stubRefProperties = $this->stubRefClass1->getPropertiesByMatcher($mockPropertyMatcher);
        $this->assertEquals(1, count($stubRefProperties));
        $this->assertInstanceOf('stubReflectionProperty', $stubRefProperties[0]);
    }

    /**
     * test that getting the interfaces works correct
     *
     * @test
     */
    public function getInterfaces()
    {
        $stubRefClasses = $this->stubRefClass1->getInterfaces();
        $this->assertEquals(1, count($stubRefClasses));
        foreach ($stubRefClasses as $stubRefClass) {
            $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
            $this->assertEquals('stubInterface', $stubRefClass->getName());
        }
        
        $stubRefClasses = $this->stubRefClass2->getInterfaces();
        $this->assertEquals(count($stubRefClasses), 0);
    }

    /**
     * test that getting the parent class works correct
     *
     * @test
     */
    public function getParentClass()
    {
        $stubRefClass = $this->stubRefClass1->getParentClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTestWithOutMethodsAndProperties', $stubRefClass->getName());
        
        $this->assertNull($this->stubRefClass2->getParentClass());
    }

    /**
     * test that getting the extension works correct
     *
     * @test
     */
    public function getExtension()
    {
        $this->assertNull($this->stubRefClass1->getExtension());
        $refClass = new stubReflectionClass('ArrayIterator');
        $this->assertInstanceOf('stubReflectionExtension', $refClass->getExtension());
    }

    /**
     * test that class loading works as expected
     *
     * @test
     */
    public function classLoading()
    {
        $refClass = new stubReflectionClass('examples::stubTestFullLoading');
        $this->assertEquals('stubTestFullLoading', $refClass->getFullQualifiedClassName());
        $refClass = new stubReflectionClass('org::stubbles::test::TestClassLoading');
        $this->assertEquals('org::stubbles::test::TestClassLoading', $refClass->getFullQualifiedClassName());
    }

    /**
     * ensure a stubReflectionPackage is returned
     *
     * @test
     */
    public function getPackage()
    {
        $package = $this->stubRefClass1->getPackage();
        $this->assertInstanceOf('stubReflectionPackage', $package);
    }

    /**
     * reflected classes are always objects
     *
     * @test
     */
    public function isObject()
    {
        $this->assertTrue($this->stubRefClass1->isObject());
    }

    /**
     * reflected classes are never primitives
     *
     * @test
     */
    public function isPrimitive()
    {
        $this->assertFalse($this->stubRefClass1->isPrimitive());
    }
}
?>