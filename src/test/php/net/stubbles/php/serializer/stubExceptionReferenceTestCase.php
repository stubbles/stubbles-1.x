<?php
/**
 * Tests for net::stubbles::php::serializer::stubExceptionReference.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @version     $Id: stubExceptionReferenceTestCase.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::php::serializer::stubExceptionReference');
/**
 * Tests for net::stubbles::php::serializer::stubExceptionReference.
 *
 * @package     stubbles
 * @subpackage  php_serializer_test
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 * @group       php
 * @group       php_serializer
 */
class stubExceptionReferenceTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubExceptionReference
     */
    protected $exceptionReference;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->exceptionReference = new stubExceptionReference('error message');
    }

    /**
     * assure that exceptionName property can be get and set
     *
     * @test
     */
    public function exceptionNameProperty()
    {
        $this->assertNull($this->exceptionReference->getReferencedExceptionName());
        $this->exceptionReference->setReferencedExceptionName('foo::bar::BazException');
        $this->assertEquals('foo::bar::BazException', $this->exceptionReference->getReferencedExceptionName());
    }
    /**
     * assure that stack trace property can be get and set
     *
     * @test
     */
    public function stackTraceProperty()
    {
        $this->assertEquals(array(), $this->exceptionReference->getReferencedStackTrace());
        $this->exceptionReference->setReferencedStackTrace(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $this->exceptionReference->getReferencedStackTrace());
    }
}
?>