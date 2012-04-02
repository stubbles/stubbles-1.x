<?php
/**
 * Test for net::stubbles::service::rest::stubRestMethodsMatcher.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @version     $Id: stubRestMethodsMatcherTestCase.php 2462 2010-01-18 15:32:49Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionMethod',
                      'net::stubbles::service::rest::stubRestMethodsMatcher'
);
/**
 * Tests for net::stubbles::service::rest::stubRestMethodsMatcher.
 *
 * @package     stubbles
 * @subpackage  service_rest_test
 * @since       1.1.0
 * @group       service
 * @group       service_rest
 */
class stubRestMethodsMatcherTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubRestMethodsMatcher
     */
    protected $restMethodsMatcher;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->restMethodsMatcher = new stubRestMethodsMatcher();
    }

    /**
     * helper method for the test
     */
    public function nonAnnotatedPublicMethod()
    {
        // intentionally empty
    }

    /**
     * helper method for the test
     */
    protected function nonPublicMethod()
    {
        // intentionally empty
    }

    /**
     * helper method for the test
     *
     * @RestMethod(requestMethod='GET')
     */
    public function annotatedPublicMethod()
    {
        // intentionally empty
    }

    /**
     * @test
     */
    public function matchesOnlyPublicMethods()
    {
        $nonAnnotatedPublicRefMethod = new ReflectionMethod(get_class($this), 'nonAnnotatedPublicMethod');
        $nonPublicRefMethod          = new ReflectionMethod(get_class($this), 'nonPublicMethod');
        $annotatedPublicRefMethod    = new ReflectionMethod(get_class($this), 'annotatedPublicMethod');
        $this->assertTrue($this->restMethodsMatcher->matchesMethod($nonAnnotatedPublicRefMethod));
        $this->assertFalse($this->restMethodsMatcher->matchesMethod($nonPublicRefMethod));
        $this->assertTrue($this->restMethodsMatcher->matchesMethod($annotatedPublicRefMethod));
    }

    /**
     * @test
     */
    public function matchesOnlyMethodsWithRestMethodAnnotation()
    {
        $nonAnnotatedPublicRefMethod = new stubReflectionMethod(get_class($this), 'nonAnnotatedPublicMethod');
        $nonPublicRefMethod          = new stubReflectionMethod(get_class($this), 'nonPublicMethod');
        $annotatedPublicRefMethod    = new stubReflectionMethod(get_class($this), 'annotatedPublicMethod');
        $this->assertFalse($this->restMethodsMatcher->matchesAnnotatableMethod($nonAnnotatedPublicRefMethod));
        $this->assertFalse($this->restMethodsMatcher->matchesAnnotatableMethod($nonPublicRefMethod));
        $this->assertTrue($this->restMethodsMatcher->matchesAnnotatableMethod($annotatedPublicRefMethod));
    }
}
?>