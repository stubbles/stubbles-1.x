<?php
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory::build().
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @version     $Id: stubAnnotationFactoryBuildTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAbstractAnnotation',
                      'net::stubbles::reflection::annotations::stubAnnotation',
                      'net::stubbles::reflection::annotations::stubAnnotationFactory'
);
class stubMethodAnnotation extends stubAbstractAnnotation implements stubAnnotation
{
    protected $foo;
    
    protected $finishCalled = false;
    
    public function finish()
    {
        $this->finishCalled = true;
    }
    
    public function wasFinishCalled()
    {
        return $this->finishCalled;
    }
    
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }
    
    public function getFoo()
    {
        return $this->foo;
    }
    
    protected function setBar($bar) {}
    
    private function setBar2($bar) {}

    public function setAnnotationName($name) {}
    public function getAnnotationName() {}
    public function getAnnotationTarget() {}
}
class stubPropertyAnnotation extends stubAbstractAnnotation implements stubAnnotation
{
    public $foo;
    protected $bar;
    private $bar2;
    public static $baz;
    public function setAnnotationName($name) {}
    public function getAnnotationName() {}
    public function getAnnotationTarget() {}
}
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory::build().
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationFactoryBuildTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * check that building the annotation works correct
     *
     * @test
     */
    public function buildWithCorrectDataAndMethod()
    {
        $stubAnnotation = new stubMethodAnnotation();
        $data           = array('foo' => 'bar');
        stubAnnotationFactory::build($stubAnnotation, $data);
        $this->assertEquals('bar', $stubAnnotation->getFoo());
        $this->assertTrue($stubAnnotation->wasFinishCalled());
    }
    
    /**
     * check that building the annotation works correct
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function buildWithProtectedMethod()
    {
        $stubAnnotation = new stubMethodAnnotation();
        $data           = array('bar' => 'baz');
        stubAnnotationFactory::build($stubAnnotation, $data);
    }
    
    /**
     * check that building the annotation works correct
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function buildWithPrivateMethod()
    {
        $stubAnnotation = new stubMethodAnnotation();
        $data           = array('bar2' => 'baz2');
        stubAnnotationFactory::build($stubAnnotation, $data);
    }

    /**
     * check that building the annotation works correct
     *
     * @test
     */
    public function buildWithCorrectDataAndProperty()
    {
        $stubAnnotation = new stubPropertyAnnotation();
        $data           = array('foo' => 'bar');
        stubAnnotationFactory::build($stubAnnotation, $data);
        $this->assertEquals('bar', $stubAnnotation->foo);
    }
    
    /**
     * check that building the annotation works correct
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function buildWithProtectedProperty()
    {
        $stubAnnotation = new stubPropertyAnnotation();
        $data           = array('bar' => 'baz');
        stubAnnotationFactory::build($stubAnnotation, $data);
    }
    
    /**
     * check that building the annotation works correct
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function buildWithPrivateProperty()
    {
        $stubAnnotation = new stubPropertyAnnotation();
        $data           = array('bar2' => 'baz2');
        stubAnnotationFactory::build($stubAnnotation, $data);
    }

    /**
     * check that building the annotation works correct
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function buildWithNoMethodAndProperty()
    {
        $stubAnnotation = new stubPropertyAnnotation();
        $data           = array('example' => 'foo');
        stubAnnotationFactory::build($stubAnnotation, $data);
    }
}
?>