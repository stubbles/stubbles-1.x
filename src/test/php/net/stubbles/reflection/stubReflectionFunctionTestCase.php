<?php
/**
 * Test for net::stubbles::reflection::stubReflectionFunction.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionFunctionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionFunction',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation'
);
/**
 * Helper annotation for the test.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class FunctionTest extends stubAbstractAnnotation implements stubAnnotation
{
    /**
     * Returns the target of the annotation as bitmap.
     *
     * @return  int
     */
    public function getAnnotationTarget()
    {
        return stubAnnotation::TARGET_FUNCTION;
    }
}
/**
 * does not return anything
 *
 * @FunctionTest()
 */
function stubTestWithOutParams() {}
/**
 * returns a string
 *
 * @param   string $param1
 * @param   mixed  $param2
 * @return  string
 */
function stubTestWithParams($param1, $param2) {}
function stubTestWithOutDocBlock() {}
/**
 * returns a class
 *
 * @return  stubReflectionFunctionTestCase
 */
function stubTestWithClassReturnType() {}
/**
 * Test for net::stubbles::reflection::stubReflectionFunction.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionFunctionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to test
     *
     * @var  stubReflectionFunction
     */
    protected $stubRefFunction1;
    /**
     * instance 2 to test
     *
     * @var  stubReflectionFunction
     */
    protected $stubRefFunction2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->stubRefFunction1 = new stubReflectionFunction('stubTestWithParams');
        $this->stubRefFunction2 = new stubReflectionFunction('stubTestWithOutParams');
    }

    /**
     * assure that instances of stubReflectionClass for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefFunction1->equals($this->stubRefFunction1));
        $this->assertTrue($this->stubRefFunction2->equals($this->stubRefFunction2));
        $stubRefFunction = new stubReflectionFunction('stubTestWithParams');
        $this->assertTrue($this->stubRefFunction1->equals($stubRefFunction));
        $this->assertTrue($stubRefFunction->equals($this->stubRefFunction1));
        $this->assertFalse($this->stubRefFunction1->equals($this->stubRefFunction2));
        $this->assertFalse($this->stubRefFunction1->equals('foo'));
        $this->assertFalse($this->stubRefFunction2->equals($this->stubRefFunction1));
        $this->assertFalse($this->stubRefFunction2->equals($stubRefFunction));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionFunction[stubTestWithParams()] {\n}\n", (string) $this->stubRefFunction1);
        $this->assertEquals("net::stubbles::reflection::stubReflectionFunction[stubTestWithOutParams()] {\n}\n", (string) $this->stubRefFunction2);
    }

    /**
     * only one of the functions has an annotation
     *
     * @test
     */
    public function hasAnnotation()
    {
        $this->assertTrue($this->stubRefFunction2->hasAnnotation('FunctionTest'));
        $this->assertFalse($this->stubRefFunction2->hasAnnotation('Other'));
    }

    /**
     * only one of the functions has an annotation
     *
     * @test
     */
    public function getAnnotation()
    {
        $this->assertInstanceOf('FunctionTest', $this->stubRefFunction2->getAnnotation('FunctionTest'));
    }

    /**
     * test that getting the parameters works correct
     *
     * @test
     */
    public function getParameters()
    {
        $stubRefParameters = $this->stubRefFunction1->getParameters();
        $this->assertEquals(2, count($stubRefParameters));
        foreach ($stubRefParameters as $stubRefParameter) {
            $this->assertInstanceOf('stubReflectionParameter', $stubRefParameter);
        }
        
        $stubRefParameters = $this->stubRefFunction2->getParameters();
        $this->assertEquals(0, count($stubRefParameters));
    }

    /**
     * test the return type hint
     *
     * @test
     */
    public function getReturnType()
    {
        $this->assertNull($this->stubRefFunction2->getReturnType());
        $this->assertSame(stubReflectionPrimitive::$STRING, $this->stubRefFunction1->getReturnType());
        $refFunction3 = new stubReflectionFunction('stubTestWithOutDocBlock');
        $this->assertNull($refFunction3->getReturnType());
        $refFunction4 = new stubReflectionFunction('stubTestWithClassReturnType');
        $refClass = $refFunction4->getReturnType();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubReflectionFunctionTestCase', $refClass->getName());
    }
}
?>