<?php
/**
 * Integration test for annotations.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @version     $Id: AnnotationTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::reflection',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation'
);
/**
 * Test class for the annotation integration test.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @IntegrationAnnotation(AnnotationClass.class)
 */
class AnnotationClass
{
    // intentionally empty
}
/**
 * Test annotation for the annotation integration test.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @IntegrationAnnotation(AnnotationClass.class)
 */
class IntegrationAnnotation extends stubAbstractAnnotation
{
    protected $value;

    public function setValue($value)
    {
        $this->value = $value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function getAnnotationTarget()
    {
        return stubAnnotation::TARGET_CLASS;
    }
}
/**
 * Integration test for annotations.
 *
 * @package     stubbles
 * @subpackage  test_integration
 * @group       integration
 */
class AnnotationTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * helper method to create the instance
     *
     * @return  AnnotationClass
     */
    protected function getInstance()
    {
        $clazz      = new stubReflectionClass('AnnotationClass');
        $annotation = $clazz->getAnnotation('IntegrationAnnotation');
        $value      = $annotation->getValue();
        return $value->newInstance();
    }

    /**
     * assure that annotations containing instances of stubReflectionClass work correct
     *
     * @link  http://stubbles.net/ticket/63
     * @test
     */
    public function ticket63()
    {
        $this->assertInstanceOf('AnnotationClass', $this->getInstance());
        // cached
        stubAnnotationCache::refresh();
        $this->assertInstanceOf('AnnotationClass', $this->getInstance());
    }
}
?>