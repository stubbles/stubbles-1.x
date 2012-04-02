<?php
/**
 * Tests for net::stubbles::lang::exceptions::stubRuntimeException.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 * @version     $Id: stubRuntimeExceptionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubRuntimeException');
/**
 * Helper class for equal() tests.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 */
class stub1stubRuntimeException extends stubRuntimeException
{
    /**
     * needs to have a class name
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'net::stubbles::lang::exceptions::test::stub1stubRuntimeException';
    }
}
/**
 * Helper class for equal() tests.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 */
class stub2stubRuntimeException extends stubRuntimeException
{
    /**
     * needs to have a class name
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'net::stubbles::lang::exceptions::test::stub2stubRuntimeException';
    }
}
/**
 * Tests for net::stubbles::lang::exceptions::stubRuntimeException.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 * @group       lang
 * @group       lang_exceptions
 */
class stubRuntimeExceptionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @var  stubRuntimeException
     */
    protected $runtimeException1;
    /**
     * instance 2 to be used for tests
     *
     * @var  stubRuntimeException
     */
    protected $runtimeException2;
    /**
     * instance 3 to be used for tests
     *
     * @var  stubRuntimeException
     */
    protected $runtimeException3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->runtimeException1 = new stub1stubRuntimeException();
        $this->runtimeException2 = new stub2stubRuntimeException();
        $this->runtimeException3 = new stubRuntimeException('message');
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->runtimeException3->getClass();
        $this->assertInstanceOf('stubReflectionObject', $refObject);
        $this->assertEquals('stubRuntimeException', $refObject->getName());
    }

    /**
     * @test
     */
    public function getClassNameReturnsFullQualifiedClassNameOfClass()
    {
        $this->assertEquals('net::stubbles::lang::exceptions::stubRuntimeException',
                            $this->runtimeException3->getClassName()
        );
    }

    /**
     * @test
     */
    public function getPackageReturnsReflectorForPackageWhereClassBelongsTo()
    {
        $refPackage = $this->runtimeException3->getPackage();
        $this->assertInstanceOf('stubReflectionPackage', $refPackage);
        $this->assertEquals('net::stubbles::lang::exceptions', $refPackage->getName());
    }

    /**
     * @test
     */
    public function getPackageNameReturnsNameOfPackageWhereClassBelongsTo()
    {
        $this->assertEquals('net::stubbles::lang::exceptions', $this->runtimeException3->getPackageName());
    }

    /**
     * @test
     */
    public function classInstanceIsEqualToItself()
    {
        $this->assertTrue($this->runtimeException1->equals($this->runtimeException1));
        $this->assertTrue($this->runtimeException2->equals($this->runtimeException2));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToInstanceOfOtherClass()
    {
        $this->assertFalse($this->runtimeException1->equals($this->runtimeException2));
        $this->assertFalse($this->runtimeException2->equals($this->runtimeException1));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToOtherInstanceOfSameClass()
    {
        $this->assertFalse($this->runtimeException1->equals(new stub1stubRuntimeException()));
        $this->assertFalse($this->runtimeException2->equals(new stub2stubRuntimeException()));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToString()
    {
        $this->assertFalse($this->runtimeException1->equals('foo'));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNumber()
    {
        $this->assertFalse($this->runtimeException1->equals(6));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanTrue()
    {
        $this->assertFalse($this->runtimeException1->equals(true));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanFalse()
    {
        $this->assertFalse($this->runtimeException1->equals(false));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNull()
    {
        $this->assertFalse($this->runtimeException1->equals(null));
    }

    /**
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("net::stubbles::lang::exceptions::stubRuntimeException {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->runtimeException3->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->runtimeException3->getTraceAsString() . "\n}\n",
                            (string) $this->runtimeException3
        );
    }
}
?>