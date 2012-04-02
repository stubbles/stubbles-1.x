<?php
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory::isApplicable().
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @version     $Id: stubAnnotationFactoryApplicableTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotationFactory');
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory::isApplicable().
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationFactoryApplicableTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * annotation to use for tests
     *
     * @var  stubAnnotation
     */
    protected $mockStubAnnotation;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockStubAnnotation = $this->getMock('stubAnnotation');
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForNothing()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(0));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClass()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForProperty()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForMethod()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForParam()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PARAM));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndProperty()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndMethod()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndParam()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_PARAM));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForPropertyAndMethod()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForPropertyAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForPropertyAndParam()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_PARAM));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForMethodAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_METHOD + stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForMethodAndParam()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_METHOD + stubAnnotation::TARGET_PARAM));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForFunctionAndParam()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_FUNCTION + stubAnnotation::TARGET_PARAM));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndPropertyAndMethod()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndPropertyAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForClassAndMethodAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_METHOD + stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForPropertyAndMethodAndFunction()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_METHOD + stubAnnotation::TARGET_FUNCTION));
        $this->assertFalse(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForAllByAddition()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_CLASS + stubAnnotation::TARGET_PROPERTY + stubAnnotation::TARGET_METHOD + stubAnnotation::TARGET_FUNCTION + stubAnnotation::TARGET_PARAM));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }

    /**
     * check that the applicable check works correct
     *
     * @test
     */
    public function isApplicableForAll()
    {
        $this->mockStubAnnotation->expects($this->any())->method('getAnnotationTarget')->will($this->returnValue(stubAnnotation::TARGET_ALL));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_CLASS));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PROPERTY));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_METHOD));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_FUNCTION));
        $this->assertTrue(stubAnnotationFactory::isApplicable($this->mockStubAnnotation, stubAnnotation::TARGET_PARAM));
    }
}
?>