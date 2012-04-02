<?php
/**
 * Test for net::stubbles::reflection::annotations::stubGenericAnnotation.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubGenericAnnotation');
/**
 * Test for net::stubbles::reflection::annotations::stubGenericAnnotation.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @since       1.6.0
 * @group       reflection
 * @group       reflection_annotations
 * @group       bug252
 */
class stubGenericAnnotationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubGenericAnnotation
     */
    protected $genericAnnotation;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->genericAnnotation = new stubGenericAnnotation();
    }

    /**
     * @test
     */
    public function isApplicableForAllTargets()
    {
        $this->assertEquals(stubAnnotation::TARGET_ALL,
                            $this->genericAnnotation->getAnnotationTarget()
        );
    }

    /**
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function callUndefinedMethodThrowsUnsupportedMethodException()
    {
        $this->genericAnnotation->invalid();
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithGet()
    {
        $this->genericAnnotation->setValue('bar');
        $this->assertEquals('bar',
                            $this->genericAnnotation->getFoo()
        );
        $this->assertEquals('bar',
                            $this->genericAnnotation->getOther()
        );
    }

    /**
     * @test
     */
    public function returnsSpecialValueForAllMethodCallsWithIs()
    {
        $this->genericAnnotation->setValue(true);
        $this->assertTrue($this->genericAnnotation->isFoo());
        $this->assertTrue($this->genericAnnotation->isOther());
    }

    /**
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function throwsUnsupportedMethodExceptionForMethodCallsWithoutGetOrIsOnSpecialValue()
    {
        $this->genericAnnotation->setValue('bar');
        $this->genericAnnotation->invalid();
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsFalseOnCheckForUnsetProperty()
    {
        $this->assertFalse($this->genericAnnotation->hasValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsTrueOnCheckForSetProperty()
    {
        $this->genericAnnotation->foo = 'hello';
        $this->assertTrue($this->genericAnnotation->hasValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsNullForUnsetProperty()
    {
        $this->assertNull($this->genericAnnotation->getValueByName('foo'));
    }

    /**
     * @test
     * @group  value_by_name
     * @since   1.7.0
     */
    public function returnsValueForSetProperty()
    {
         $this->genericAnnotation->foo = 'hello';
        $this->assertEquals('hello', $this->genericAnnotation->getValueByName('foo'));
    }

    /**
     * @test
     */
    public function returnsNullForUnsetGetProperty()
    {
        $this->assertNull($this->genericAnnotation->getFoo());
    }

    /**
     * @test
     */
    public function returnsFalseForUnsetBooleanProperty()
    {
        $this->assertFalse($this->genericAnnotation->isFoo());
    }

    /**
     * @test
     */
    public function returnsValueOfGetProperty()
    {
        $this->genericAnnotation->foo = 'bar';
        $this->assertEquals('bar',
                            $this->genericAnnotation->getFoo()
        );
    }

    /**
     * @test
     */
    public function returnsFirstArgumentIfGetPropertyNotSet()
    {
        $this->assertEquals('bar',
                            $this->genericAnnotation->getFoo('bar')
        );
    }

    /**
     * @test
     */
    public function returnsValueOfBooleanProperty()
    {
        $this->genericAnnotation->foo = true;
        $this->assertTrue($this->genericAnnotation->isFoo());
    }

    /**
     * @test
     */
    public function returnTrueForValueCheckIfValueSet()
    {
        $this->genericAnnotation->setValue('bar');
        $this->assertTrue($this->genericAnnotation->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfValueNotSet()
    {
        $this->assertFalse($this->genericAnnotation->hasValue());
    }

    /**
     * @test
     */
    public function returnFalseForValueCheckIfAnotherPropertySet()
    {
        $this->genericAnnotation->foo = 'bar';
        $this->assertFalse($this->genericAnnotation->hasValue());
    }

    /**
     * @test
     */
    public function returnTrueForPropertyCheckIfPropertySet()
    {
        $this->genericAnnotation->foo = 'bar';
        $this->genericAnnotation->baz = true;
        $this->assertTrue($this->genericAnnotation->hasFoo());
        $this->assertTrue($this->genericAnnotation->hasBaz());
    }

    /**
     * @test
     */
    public function returnFalseForPropertyCheckIfPropertyNotSet()
    {
        $this->assertFalse($this->genericAnnotation->hasFoo());
        $this->assertFalse($this->genericAnnotation->hasBaz());
    }

    /**
     * @test
     */
    public function canAccessPropertyAsMethod()
    {
        $this->genericAnnotation->foo = 'bar';
        $this->assertEquals('bar',
                            $this->genericAnnotation->foo()
        );
        $this->genericAnnotation->baz = true;
    }

    /**
     * @test
     */
    public function canAccessBooleanPropertyAsMethod()
    {
        $this->genericAnnotation->foo = true;
        $this->assertTrue($this->genericAnnotation->foo());
    }
}
?>