<?php
/**
 * Test for net::stubbles::reflection::stubReflectionMethod.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionMethodTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionMethod');
/**
 * class to be used for the test
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubTest
{
    /**
     * does not return anything
     */
    public function methodWithoutParams()
    {
        // nothing to to here
    }

    /**
     * returns a scalar value
     *
     * @param   string  $param1
     * @param   mixed   $param2
     * @return  string
     */
    public function methodWithParams($param1, $param2)
    {
        // nothing to to here
    }
}
/**
 * another class to be used for the test
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubTest2 extends stubTest
{
    /**
     * returns a class instance
     *
     * @param   int       $param3
     * @return  stubTest
     */
    public function methodWithParams2($param3)
    {
        // nothing to to here
    }
}
/**
 * Test for net::stubbles::reflection::stubReflectionMethod.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionMethodTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @var  stubReflectionMethod
     */
    protected $stubRefMethod1;
    /**
     * instance 2 to test
     *
     * @var  stubReflectionMethod
     */
    protected $stubRefMethod2;
    /**
     * instance 3 to test
     *
     * @var  stubReflectionMethod
     */
    protected $stubRefMethod3;
    /**
     * instance 4 to test
     *
     * @var  stubReflectionMethod
     */
    protected $stubRefMethod4;
    /**
     * instance 5 to test
     *
     * @var  stubReflectionMethod
     */
    protected $stubRefMethod5;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->stubRefMethod1 = new stubReflectionMethod('stubTest', 'methodWithoutParams');
        $this->stubRefMethod2 = new stubReflectionMethod('stubTest', 'methodWithParams');
        $this->stubRefMethod3 = new stubReflectionMethod('stubTest2', 'methodWithoutParams');
        $this->stubRefMethod4 = new stubReflectionMethod('stubTest2', 'methodWithParams');
        $this->stubRefMethod5 = new stubReflectionMethod('stubTest2', 'methodWithParams2');
    }

    /**
     * assure that instances of stubReflectionClass for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefMethod1->equals($this->stubRefMethod1));
        $this->assertTrue($this->stubRefMethod2->equals($this->stubRefMethod2));
        $this->assertTrue($this->stubRefMethod3->equals($this->stubRefMethod3));
        $this->assertTrue($this->stubRefMethod4->equals($this->stubRefMethod4));
        $this->assertTrue($this->stubRefMethod5->equals($this->stubRefMethod5));
        $stubRefMethod = new stubReflectionMethod('stubTest', 'methodWithoutParams');
        $this->assertTrue($this->stubRefMethod1->equals($stubRefMethod));
        $this->assertTrue($stubRefMethod->equals($this->stubRefMethod1));
        $this->assertFalse($this->stubRefMethod1->equals('foo'));
        $this->assertFalse($this->stubRefMethod1->equals($this->stubRefMethod2));
        $this->assertFalse($this->stubRefMethod1->equals($this->stubRefMethod3));
        $this->assertFalse($this->stubRefMethod1->equals($this->stubRefMethod4));
        $this->assertFalse($this->stubRefMethod1->equals($this->stubRefMethod5));
        $this->assertFalse($this->stubRefMethod2->equals($this->stubRefMethod1));
        $this->assertFalse($this->stubRefMethod2->equals($this->stubRefMethod3));
        $this->assertFalse($this->stubRefMethod2->equals($this->stubRefMethod4));
        $this->assertFalse($this->stubRefMethod2->equals($this->stubRefMethod5));
        $this->assertFalse($this->stubRefMethod2->equals($stubRefMethod));
        $this->assertFalse($this->stubRefMethod3->equals($this->stubRefMethod1));
        $this->assertFalse($this->stubRefMethod3->equals($this->stubRefMethod2));
        $this->assertFalse($this->stubRefMethod3->equals($this->stubRefMethod4));
        $this->assertFalse($this->stubRefMethod3->equals($this->stubRefMethod5));
        $this->assertFalse($this->stubRefMethod3->equals($stubRefMethod));
        $this->assertFalse($this->stubRefMethod4->equals($this->stubRefMethod1));
        $this->assertFalse($this->stubRefMethod4->equals($this->stubRefMethod2));
        $this->assertFalse($this->stubRefMethod4->equals($this->stubRefMethod3));
        $this->assertFalse($this->stubRefMethod4->equals($this->stubRefMethod5));
        $this->assertFalse($this->stubRefMethod4->equals($stubRefMethod));
        $this->assertFalse($this->stubRefMethod5->equals($this->stubRefMethod1));
        $this->assertFalse($this->stubRefMethod5->equals($this->stubRefMethod2));
        $this->assertFalse($this->stubRefMethod5->equals($this->stubRefMethod3));
        $this->assertFalse($this->stubRefMethod5->equals($this->stubRefMethod4));
        $this->assertFalse($this->stubRefMethod5->equals($stubRefMethod));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionMethod[stubTest::methodWithoutParams()] {\n}\n", (string) $this->stubRefMethod1);
        $this->assertEquals("net::stubbles::reflection::stubReflectionMethod[stubTest::methodWithParams()] {\n}\n", (string) $this->stubRefMethod2);
        $this->assertEquals("net::stubbles::reflection::stubReflectionMethod[stubTest2::methodWithoutParams()] {\n}\n", (string) $this->stubRefMethod3);
        $this->assertEquals("net::stubbles::reflection::stubReflectionMethod[stubTest2::methodWithParams()] {\n}\n", (string) $this->stubRefMethod4);
        $this->assertEquals("net::stubbles::reflection::stubReflectionMethod[stubTest2::methodWithParams2()] {\n}\n", (string) $this->stubRefMethod5);
    }

    /**
     * test that getting the declaring class works correct
     *
     * @test
     */
    public function getDeclaringClass()
    {
        $stubRefClass = $this->stubRefMethod1->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTest', $stubRefClass->getName());
        
        $stubRefClass = $this->stubRefMethod2->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTest', $stubRefClass->getName());
        
        $stubRefClass = $this->stubRefMethod3->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTest', $stubRefClass->getName());
        
        $stubRefClass = $this->stubRefMethod4->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTest', $stubRefClass->getName());
        
        $stubRefClass = $this->stubRefMethod5->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        $this->assertEquals('stubTest2', $stubRefClass->getName());
    }

    /**
     * test that getting the parameters works correct
     *
     * @test
     */
    public function getParameters()
    {
        $stubRefParameters = $this->stubRefMethod1->getParameters();
        $this->assertEquals(0, count($stubRefParameters));
        
        $stubRefParameters = $this->stubRefMethod2->getParameters();
        $this->assertEquals(2, count($stubRefParameters));
        foreach ($stubRefParameters as $stubRefParameter) {
            $this->assertInstanceOf('stubReflectionParameter', $stubRefParameter);
        }
        
        $stubRefParameters = $this->stubRefMethod3->getParameters();
        $this->assertEquals(0, count($stubRefParameters));

        $stubRefParameters = $this->stubRefMethod4->getParameters();
        $this->assertEquals(2, count($stubRefParameters));
        foreach ($stubRefParameters as $stubRefParameter) {
            $this->assertInstanceOf('stubReflectionParameter', $stubRefParameter);
        }
        
        $stubRefParameters = $this->stubRefMethod5->getParameters();
        $this->assertEquals(1, count($stubRefParameters));
        foreach ($stubRefParameters as $stubRefParameter) {
            $this->assertInstanceOf('stubReflectionParameter', $stubRefParameter);
        }
    }

    /**
     * test the return type hint
     *
     * @test
     */
    public function getReturnType()
    {
        $this->assertNull($this->stubRefMethod1->getReturnType());
        $this->assertSame(stubReflectionPrimitive::$STRING, $this->stubRefMethod2->getReturnType());
        $this->assertNull($this->stubRefMethod3->getReturnType());
        $this->assertSame(stubReflectionPrimitive::$STRING, $this->stubRefMethod4->getReturnType());
        $refClass = $this->stubRefMethod5->getReturnType();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubTest', $refClass->getName());
    }

    /**
     * test instantiation using reflection class instance
     *
     * @test
     */
    public function instantiationWithReflectionClass()
    {
        $refClass1 = new stubReflectionClass('stubTest');
        $refClass2 = new stubReflectionClass('stubTest2');
        $stubRefMethod1 = new stubReflectionMethod($refClass1, 'methodWithoutParams');
        $this->assertSame($refClass1, $stubRefMethod1->getDeclaringClass());
        $stubRefMethod2 = new stubReflectionMethod($refClass1, 'methodWithParams');
        $this->assertSame($refClass1, $stubRefMethod2->getDeclaringClass());
        $stubRefMethod3 = new stubReflectionMethod($refClass2, 'methodWithoutParams');
        $this->assertEquals('stubTest', $stubRefMethod3->getDeclaringClass()->getName());
        $stubRefMethod4 = new stubReflectionMethod($refClass2, 'methodWithParams');
        $this->assertEquals('stubTest', $stubRefMethod4->getDeclaringClass()->getName());
        $stubRefMethod5 = new stubReflectionMethod($refClass2, 'methodWithParams2');
        $this->assertSame($refClass2, $stubRefMethod5->getDeclaringClass());
    }
}
?>