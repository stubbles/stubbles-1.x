<?php
/**
 * Test for net::stubbles::reflection::stubReflectionParameter.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionParameterTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionParameter',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation'
);
/**
 * annotation for parameters
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubParamAnnoAnnotation extends stubAbstractAnnotation implements stubAnnotation
{
    /**
     * Returns the target of the annotation as bitmap.
     *
     * @return  int
     */
    public function getAnnotationTarget()
    {
        return stubAnnotation::TARGET_PARAM;
    }
}
/**
 * a function
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @param       mixed  $param
 * @ParamAnno{param}
 */
function stubtest_function($param)
{
    // nothing to do
}
/**
 * a class for tests
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubParamTest
{
    /**
     * a method
     *
     * @param  mixed  $param
     * @ParamAnno{param}
     */
    function paramTest($param)
    {
        // nothing to do
    }
}
/**
 * another class for tests
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class stubParamTest2 extends stubParamTest
{
    /**
     * another method
     *
     * @param  stubParamTest  $param2
     * @ParamAnno{param2}
     */
    function paramTest2(stubParamTest $param2)
    {
        // nothing to do
    }

    /**
     * one more method
     *
     * @param  stubParamTest2  $param2
     * @ParamAnno{param}
     */
    function paramTest3(self $param2)
    {
        // nothing to do
    }
}
/**
 * Test for net::stubbles::reflection::stubReflectionParameter.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionParameterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubReflectionParameter
     */
    protected $stubRefParamFunction;
    /**
     * instance to test
     *
     * @var  stubReflectionParameter
     */
    protected $stubRefParamMethod1;
    /**
     * instance to test
     *
     * @var  stubReflectionParameter
     */
    protected $stubRefParamMethod2;
    /**
     * instance to test
     *
     * @var  stubReflectionParameter
     */
    protected $stubRefParamMethod3;
    /**
     * instance to test
     *
     * @var  stubReflectionParameter
     */
    protected $stubRefParamMethod4;

    /**
     * create the test environment
     */
    public function setUp()
    {
        $this->stubRefParamFunction = new stubReflectionParameter('stubtest_function', 'param');
        $this->stubRefParamMethod1  = new stubReflectionParameter(array('stubParamTest', 'paramTest'), 'param');
        $this->stubRefParamMethod2  = new stubReflectionParameter(array('stubParamTest2', 'paramTest'), 'param');
        $this->stubRefParamMethod3  = new stubReflectionParameter(array('stubParamTest2', 'paramTest2'), 'param2');
        $this->stubRefParamMethod4  = new stubReflectionParameter(array('stubParamTest2', 'paramTest3'), 'param2');
    }

    /**
     * assure that annotations are handled correctly
     *
     * @test
     */
    public function annotations()
    {
        $this->assertTrue($this->stubRefParamFunction->hasAnnotation('ParamAnno'));
        $this->assertInstanceOf('stubParamAnnoAnnotation', $this->stubRefParamFunction->getAnnotation('ParamAnno'));
        $this->assertTrue($this->stubRefParamMethod1->hasAnnotation('ParamAnno'));
        $this->assertInstanceOf('stubParamAnnoAnnotation', $this->stubRefParamMethod1->getAnnotation('ParamAnno'));
        $this->assertTrue($this->stubRefParamMethod2->hasAnnotation('ParamAnno'));
        $this->assertInstanceOf('stubParamAnnoAnnotation', $this->stubRefParamMethod2->getAnnotation('ParamAnno'));
        $this->assertTrue($this->stubRefParamMethod3->hasAnnotation('ParamAnno'));
        $this->assertInstanceOf('stubParamAnnoAnnotation', $this->stubRefParamMethod3->getAnnotation('ParamAnno'));
        $this->assertFalse($this->stubRefParamMethod4->hasAnnotation('ParamAnno'));
    }

    /**
     * assure that annotations are handled correctly
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function noAnnotations()
    {
        $this->stubRefParamMethod4->getAnnotation('ParamAnno');
    }

    /**
     * assure that instances of stubReflectionClass for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefParamFunction->equals($this->stubRefParamFunction));
        $this->assertTrue($this->stubRefParamMethod1->equals($this->stubRefParamMethod1));
        $this->assertTrue($this->stubRefParamMethod2->equals($this->stubRefParamMethod2));
        $this->assertTrue($this->stubRefParamMethod3->equals($this->stubRefParamMethod3));
        $this->assertTrue($this->stubRefParamMethod4->equals($this->stubRefParamMethod4));
        $stubRefParamFunction = new stubReflectionParameter('stubtest_function', 'param');
        $this->assertTrue($this->stubRefParamFunction->equals($stubRefParamFunction));
        $this->assertTrue($stubRefParamFunction->equals($this->stubRefParamFunction));
        $stubRefParamMethod  = new stubReflectionParameter(array('stubParamTest', 'paramTest'), 'param');
        $this->assertTrue($this->stubRefParamMethod1->equals($stubRefParamMethod));
        $this->assertTrue($stubRefParamMethod->equals($this->stubRefParamMethod1));
        $this->assertFalse($this->stubRefParamFunction->equals('foo'));
        $this->assertFalse($this->stubRefParamFunction->equals($this->stubRefParamMethod1));
        $this->assertFalse($this->stubRefParamFunction->equals($this->stubRefParamMethod2));
        $this->assertFalse($this->stubRefParamFunction->equals($this->stubRefParamMethod3));
        $this->assertFalse($this->stubRefParamFunction->equals($this->stubRefParamMethod4));
        $this->assertFalse($this->stubRefParamMethod1->equals($this->stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod1->equals($this->stubRefParamMethod2));
        $this->assertFalse($this->stubRefParamMethod1->equals($this->stubRefParamMethod3));
        $this->assertFalse($this->stubRefParamMethod1->equals($this->stubRefParamMethod4));
        $this->assertFalse($this->stubRefParamMethod1->equals($stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod2->equals($this->stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod2->equals($this->stubRefParamMethod1));
        $this->assertFalse($this->stubRefParamMethod2->equals($this->stubRefParamMethod3));
        $this->assertFalse($this->stubRefParamMethod2->equals($this->stubRefParamMethod4));
        $this->assertFalse($this->stubRefParamMethod2->equals($stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod2->equals($stubRefParamMethod));
        $this->assertFalse($this->stubRefParamMethod3->equals($this->stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod3->equals($this->stubRefParamMethod1));
        $this->assertFalse($this->stubRefParamMethod3->equals($this->stubRefParamMethod2));
        $this->assertFalse($this->stubRefParamMethod3->equals($this->stubRefParamMethod4));
        $this->assertFalse($this->stubRefParamMethod3->equals($stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod3->equals($stubRefParamMethod));
        $this->assertFalse($this->stubRefParamMethod4->equals($this->stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod4->equals($this->stubRefParamMethod1));
        $this->assertFalse($this->stubRefParamMethod4->equals($this->stubRefParamMethod2));
        $this->assertFalse($this->stubRefParamMethod4->equals($this->stubRefParamMethod3));
        $this->assertFalse($this->stubRefParamMethod4->equals($stubRefParamFunction));
        $this->assertFalse($this->stubRefParamMethod4->equals($stubRefParamMethod));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionParameter[stubtest_function(): Argument param] {\n}\n", (string) $this->stubRefParamFunction);
        $this->assertEquals("net::stubbles::reflection::stubReflectionParameter[stubParamTest::paramTest(): Argument param] {\n}\n", (string) $this->stubRefParamMethod1);
        $this->assertEquals("net::stubbles::reflection::stubReflectionParameter[stubParamTest2::paramTest(): Argument param] {\n}\n", (string) $this->stubRefParamMethod2);
        $this->assertEquals("net::stubbles::reflection::stubReflectionParameter[stubParamTest2::paramTest2(): Argument param2] {\n}\n", (string) $this->stubRefParamMethod3);
        $this->assertEquals("net::stubbles::reflection::stubReflectionParameter[stubParamTest2::paramTest3(): Argument param2] {\n}\n", (string) $this->stubRefParamMethod4);
    }

    /**
     * test that getting the functions works correct
     *
     * @test
     */
    public function getDeclaringClass()
    {
        $this->assertNull($this->stubRefParamFunction->getDeclaringClass());
        $refClass = $this->stubRefParamMethod1->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest', $refClass->getName());
        $refClass = $this->stubRefParamMethod2->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest', $refClass->getName());
        $refClass = $this->stubRefParamMethod3->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest2', $refClass->getName());
        $refClass = $this->stubRefParamMethod4->getDeclaringClass();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest2', $refClass->getName());
    }

    /**
     * test that getting the type (class) hints works correct
     *
     * @test
     */
    public function getClass()
    {
        $this->assertNull($this->stubRefParamFunction->getClass());
        $this->assertNull($this->stubRefParamMethod1->getClass());
        $this->assertNull($this->stubRefParamMethod2->getClass());
        $refClass = $this->stubRefParamMethod3->getClass();
       $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest', $refClass->getName());
        $refClass = $this->stubRefParamMethod4->getClass();
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('stubParamTest2', $refClass->getName());
    }
}
?>