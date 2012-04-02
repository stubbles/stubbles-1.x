<?php
/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 * @version     $Id: stubAnnotationFactoryTestCase.php 3273 2011-12-09 15:07:44Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotationFactory',
                      'net::stubbles::reflection::annotations::stubAnnotation',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation',
                      'net::stubbles::reflection::stubReflectionClass'
);

/**
 * Test Annotation
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_test
 */
class MyAnnotation extends stubAbstractAnnotation implements stubAnnotation
{
    public $foo;
    public $argh;
    public $veggie;

    /**
     * Returns the target of the annotation as bitmap.
     *
     * @return  int
     */
    public function getAnnotationTarget() { return stubAnnotation::TARGET_ALL; }
}

/**
 * Interface for AnotherAnnotation
 *
 */
interface CastedAnnotation { }

/**
 * Test Annotation
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class AnotherAnnotation extends stubAbstractAnnotation implements stubAnnotation, CastedAnnotation
{
    public $value;
    public function getAnnotationTarget() { return stubAnnotation::TARGET_ALL; }
}

/**
 * Test Annotation
 *
 * @package     stubbles
 * @subpackage  reflection_test
 */
class EmptyAnnotation extends stubAbstractAnnotation implements stubAnnotation
{
    public function getAnnotationTarget() { return stubAnnotation::TARGET_CLASS; }
}

/**
 * Simple test class with an annotation
 *
 * @MyAnnotation(
 *     foo='bar',
 *     argh=true,
 *     veggie='cucumber'
 * )
 */
class AnyTestClass {
}

/**
 * Annotation with class parameter
 *
 * @MyAnnotation(
 *     foo=AnyTestClass.class
 * )
 */
class AnotherTestClass {
}

/**
 * Annotation without "annotation" in its name
 *
 * @My(foo='bar')
 */
class OneMoreTestClass { }

/**
 * Test for net::stubbles::reflection::annotations::stubAnnotationFactory.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * example comment used for tests
     *
     * @var  string
     */
    protected $comment = "/**\n * a test docblock\n * \n * \n * @param  string  \$foo\n * @StubAnnotation(foo = 'blub');\n */";

    protected $commentComplex = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation(
 *     foo="bar",
 *     argh=45, veggie="tomato"
 * )
 * @AnotherAnnotation(true)
 * @CastedAnnotation[AnotherAnnotation](false)
 * @EmptyAnnotation
 * @AnnotationWithoutClassWithoutValue
 * @AnnotationWithoutClassWithSingleValue(foo)
 * @AnnotationWithoutClassWithMultipleValues(foo="bar", optional=true)
 * @AnnotationWithoutClassWithoutValueCasted[CastedWithoutClass]
 */';

    protected $commentWithClass = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation(
 *     foo=AnyTestClass.class
 * )
 */';
protected $commentComplexForArgument = '/**
 * Foobar bla
 *
 * @access public
 * @MyAnnotation{foo}(
 *     foo="bar",
 *     argh=45, veggie="tomato"
 * )
 * @AnotherAnnotation{foo}(true)
 * @CastedAnnotation{foo}[AnotherAnnotation](false)
 * @AnotherAnnotation{bar}
 * @MyAnnotation{bar}(
 *     foo=AnyTestClass.class
 * )
 */';

    /**
     * test that checking if an annotation is present works as expected
     *
     * @test
     */
    public function has()
    {
        $this->assertFalse(stubAnnotationFactory::has($this->comment, 'ExampleAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__));
        $this->assertFalse(stubAnnotationFactory::has($this->comment, 'StubAnno', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__));

        $this->assertTrue(stubAnnotationFactory::has($this->commentComplex, 'MyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__));
        $this->assertTrue(stubAnnotationFactory::has($this->commentComplex, 'AnotherAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__));
        $this->assertTrue(stubAnnotationFactory::has($this->commentComplex, 'EmptyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__));

        $this->assertFalse(stubAnnotationFactory::has($this->commentComplex, 'EmptyAnnotation', stubAnnotation::TARGET_FUNCTION, 'MyFunction', __FILE__));
    }

    /**
     * test that bug #77 will never occur again
     *
     * @link  http://stubbles.net/ticket/77
     *
     * @test
     */
    public function bug77()
    {
        // clear the cache from both annotations first
        stubAnnotationCache::remove(stubAnnotation::TARGET_CLASS, __FILE__ . '1' . '::' . 'MyClass', 'MyAnnotation');
        stubAnnotationCache::remove(stubAnnotation::TARGET_CLASS, __FILE__ . '2' . '::' . 'MyClass', 'MyAnnotation');
        
        $myAnnotation1 = stubAnnotationFactory::create($this->commentComplex, 'MyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__ . '1');
        $this->assertTrue(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__ . '1' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertFalse(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '1' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertFalse(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__ . '2' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertFalse(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '2' . '::' . 'MyClass', 'MyAnnotation'));
        $myAnnotation2 = stubAnnotationFactory::create($this->commentComplex, 'MyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__ . '2');
        $this->assertTrue(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__ . '1' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertFalse(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '1' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertTrue(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__ . '2' . '::' . 'MyClass', 'MyAnnotation'));
        $this->assertFalse(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '2' . '::' . 'MyClass', 'MyAnnotation'));
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     */
    public function create()
    {
        $myAnnotation = stubAnnotationFactory::create($this->commentComplex, 'MyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('MyAnnotation', $myAnnotation);
        $this->assertEquals('bar', $myAnnotation->foo);
        $this->assertEquals('45', $myAnnotation->argh);
        $this->assertEquals('tomato', $myAnnotation->veggie);

        $anotherAnnotation = stubAnnotationFactory::create($this->commentComplex, 'AnotherAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('AnotherAnnotation', $anotherAnnotation);
        $this->assertTrue($anotherAnnotation->value);

        $emptyAnnotation = stubAnnotationFactory::create($this->commentComplex, 'EmptyAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('EmptyAnnotation', $emptyAnnotation);

        $castedAnnotation = stubAnnotationFactory::create($this->commentComplex, 'CastedAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('AnotherAnnotation', $castedAnnotation);
        $this->assertFalse($castedAnnotation->value);

        $myAnnotation = stubAnnotationFactory::create($this->commentWithClass, 'MyAnnotation', stubAnnotation::TARGET_CLASS, 'AnotherClass', __FILE__);
        $this->assertInstanceOf('MyAnnotation', $myAnnotation);
        $this->assertInstanceOf('stubReflectionClass', $myAnnotation->foo);
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function createShouldFail()
    {
        stubAnnotationFactory::create($this->commentComplex, 'NonExisting', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     */
    public function createForArgument()
    {
        $myAnnotation = stubAnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#foo', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
        $this->assertInstanceOf('MyAnnotation', $myAnnotation);
        $this->assertEquals('bar', $myAnnotation->foo);
        $this->assertEquals('45', $myAnnotation->argh);
        $this->assertEquals('tomato', $myAnnotation->veggie);

        $anotherAnnotation = stubAnnotationFactory::create($this->commentComplexForArgument, 'AnotherAnnotation#foo', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
        $this->assertInstanceOf('AnotherAnnotation', $anotherAnnotation);
        $this->assertTrue($anotherAnnotation->value);

        $castedAnnotation = stubAnnotationFactory::create($this->commentComplexForArgument, 'CastedAnnotation#foo', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
        $this->assertInstanceOf('AnotherAnnotation', $castedAnnotation);
        $this->assertFalse($castedAnnotation->value);

        $emptyAnnotation = stubAnnotationFactory::create($this->commentComplexForArgument, 'AnotherAnnotation#bar', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
        $this->assertInstanceOf('AnotherAnnotation', $emptyAnnotation);

        $myAnnotation = stubAnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#bar', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
        $this->assertInstanceOf('MyAnnotation', $myAnnotation);
        $this->assertInstanceOf('stubReflectionClass', $myAnnotation->foo);
    }

    /**
     * test that creating an annotation works as expected
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function createForArgumentShouldFail()
    {
        stubAnnotationFactory::create($this->commentComplexForArgument, 'MyAnnotation#baz', stubAnnotation::TARGET_PARAM, 'MyClass::baz()', __FILE__);
    }

    /**
     * test that the cache works as expected
     *
     * @test
     */
    public function reflectionClass()
    {
        $class = new stubReflectionClass('AnotherTestClass');
        $anno  = $class->getAnnotation('MyAnnotation');
    }

    /**
     * test that the cache works as expected
     *
     * @test
     */
    public function cache()
    {
        $class = new stubReflectionClass('AnyTestClass');
        $anno  = $class->getAnnotation('MyAnnotation');

        // assert the values read from the annotation
        $this->assertInstanceOf('MyAnnotation', $anno);
        $this->assertEquals('bar', $anno->foo);
        $this->assertTrue($anno->argh);
        $this->assertEquals('cucumber', $anno->veggie);

        // change the value
        $anno->veggie = 'tomato';
        $this->assertEquals('tomato', $anno->veggie);

        // re-fetch
        $anno2  = $class->getAnnotation('MyAnnotation');
        $this->assertInstanceOf('MyAnnotation', $anno2);
        $this->assertEquals('bar', $anno2->foo);
        $this->assertTrue($anno2->argh);
        $this->assertEquals('cucumber', $anno2->veggie);

        // fetch with a new stubAnnotationClass instance
        $class2 = new stubReflectionClass('AnyTestClass');
        $anno3  = $class2->getAnnotation('MyAnnotation');

        $this->assertInstanceOf('MyAnnotation', $anno);
        $this->assertEquals('bar', $anno3->foo);
        $this->assertTrue($anno3->argh);
        $this->assertEquals('cucumber', $anno3->veggie);
    }

    /**
     * ensure that information about a non-existing annotation is cached as well
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function cachingOfNonExistingAnnotations()
    {
        // make sure that the information is really not in the cache
        stubAnnotationCache::remove(stubAnnotation::TARGET_CLASS, __FILE__ . '::' . 'MyClass', 'NonExistingAnnotation');
        $this->assertFalse(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__  . '::' . 'MyClass', 'NonExistingAnnotation'));
        $this->assertFalse(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '::' . 'MyClass', 'NonExistingAnnotation'));
        try {
            $annotation = stubAnnotationFactory::create($this->commentComplex, 'NonExistingAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        } catch (Exception $e) {
            $this->assertFalse(stubAnnotationCache::has(stubAnnotation::TARGET_CLASS, __FILE__  . '::' . 'MyClass', 'NonExistingAnnotation'));
            $this->assertTrue(stubAnnotationCache::hasNot(stubAnnotation::TARGET_CLASS, __FILE__ . '::' . 'MyClass', 'NonExistingAnnotation'));
            // now the exception will be thrown
            $annotation = stubAnnotationFactory::create($this->commentComplex, 'NonExistingAnnotation', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
            return;
        }
        
        $this->fail('Found NonExistingAnnotation while this annotation should not be present.');
    }

    /**
     * use the annotation but without any prefix and without the postfix "Annotation"
     *
     * @test
     */
    public function noPrefixNoAnnotationInAnnotation()
    {
        $class = new stubReflectionClass('OneMoreTestClass');
        $this->assertTrue($class->hasAnnotation('My'));
        $anno  = $class->getAnnotation('My');
        $this->assertInstanceOf('MyAnnotation', $anno);
        $this->assertEquals('bar', $anno->foo);
    }

    /**
     * @test
     * @since  1.6.0
     * @group  bug252
     */
    public function createGenericAnnotationForAnnotationWithoutClassWithoutValue()
    {
        $annotation = stubAnnotationFactory::create($this->commentComplex, 'AnnotationWithoutClassWithoutValue', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('stubGenericAnnotation', $annotation);
        $this->assertEquals('AnnotationWithoutClassWithoutValue', $annotation->getAnnotationName());
    }

    /**
     * @test
     * @since  1.6.0
     * @group  bug252
     */
    public function castedAnnotationHasCastedName()
    {
        $annotation = stubAnnotationFactory::create($this->commentComplex, 'AnnotationWithoutClassWithoutValueCasted', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('stubGenericAnnotation', $annotation);
        $this->assertEquals('CastedWithoutClass', $annotation->getAnnotationName());
    }

    /**
     * @test
     * @since  1.6.0
     * @group  bug252
     */
    public function createGenericAnnotationForAnnotationWithoutClassWithSingleValue()
    {
        $annotation = stubAnnotationFactory::create($this->commentComplex, 'AnnotationWithoutClassWithSingleValue', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('stubGenericAnnotation', $annotation);
        $this->assertEquals('foo', $annotation->getValue());
    }

    /**
     * @test
     * @since  1.6.0
     * @group  bug252
     */
    public function createGenericAnnotationForAnnotationWithoutClassWithMultipleValues()
    {
        $annotation = stubAnnotationFactory::create($this->commentComplex, 'AnnotationWithoutClassWithMultipleValues', stubAnnotation::TARGET_CLASS, 'MyClass', __FILE__);
        $this->assertInstanceOf('stubGenericAnnotation', $annotation);
        $this->assertEquals('bar', $annotation->getFoo());
        $this->assertTrue($annotation->isOptional());
    }
}
?>