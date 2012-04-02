<?php
/**
 * Tests for net::stubbles::lang::exceptions::stubException.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 * @version     $Id: stubExceptionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubException');
/**
 * Helper class for equal() tests.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 */
class stub1stubException extends stubException
{
    /**
     * needs to have a class name
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'net::stubbles::lang::exceptions::test::stub1stubException';
    }
}
/**
 * Helper class for equal() tests.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 */
class stub2stubException extends stubException
{
    /**
     * needs to have a class name
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'net::stubbles::lang::exceptions::test::stub2stubException';
    }
}
/**
 * Tests for net::stubbles::lang::exceptions::stubException.
 *
 * @package     stubbles
 * @subpackage  lang_exceptions_test
 * @group       lang
 * @group       lang_exceptions
 */
class stubExceptionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @var  stubException
     */
    protected $stubException1;
    /**
     * instance 2 to be used for tests
     *
     * @var  stubException
     */
    protected $stubException2;
    /**
     * instance 3 to be used for tests
     *
     * @var  stubException
     */
    protected $stubException3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stubException1 = new stub1stubException();
        $this->stubException2 = new stub2stubException();
        $this->stubException3 = new stubException('message');
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->stubException3->getClass();
        $this->assertInstanceOf('stubReflectionObject', $refObject);
        $this->assertEquals('stubException', $refObject->getName());
    }

    /**
     * @test
     */
    public function getClassNameReturnsFullQualifiedClassNameOfClass()
    {
        $this->assertEquals('net::stubbles::lang::exceptions::stubException',
                            $this->stubException3->getClassName()
        );
    }

    /**
     * @test
     */
    public function getPackageReturnsReflectorForPackageWhereClassBelongsTo()
    {
        $refPackage = $this->stubException3->getPackage();
        $this->assertInstanceOf('stubReflectionPackage', $refPackage);
        $this->assertEquals('net::stubbles::lang::exceptions', $refPackage->getName());
    }

    /**
     * @test
     */
    public function getPackageNameReturnsNameOfPackageWhereClassBelongsTo()
    {
        $this->assertEquals('net::stubbles::lang::exceptions', $this->stubException3->getPackageName());
    }

    /**
     * @test
     */
    public function classInstanceIsEqualToItself()
    {
        $this->assertTrue($this->stubException1->equals($this->stubException1));
        $this->assertTrue($this->stubException2->equals($this->stubException2));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToInstanceOfOtherClass()
    {
        $this->assertFalse($this->stubException1->equals($this->stubException2));
        $this->assertFalse($this->stubException2->equals($this->stubException1));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToOtherInstanceOfSameClass()
    {
        $this->assertFalse($this->stubException1->equals(new stub1stubException()));
        $this->assertFalse($this->stubException2->equals(new stub2stubException()));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToString()
    {
        $this->assertFalse($this->stubException1->equals('foo'));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNumber()
    {
        $this->assertFalse($this->stubException1->equals(6));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanTrue()
    {
        $this->assertFalse($this->stubException1->equals(true));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToBooleanFalse()
    {
        $this->assertFalse($this->stubException1->equals(false));
    }

    /**
     * @test
     */
    public function classInstanceIsNotEqualToNull()
    {
        $this->assertFalse($this->stubException1->equals(null));
    }

    /**
     * string representation should contain some useful informations
     *
     * @test
     */
    public function toStringResult()
    {
        $this->assertEquals("net::stubbles::lang::exceptions::stubException {\n    message(string): message\n    file(string): " . __FILE__ . "\n    line(integer): " . $this->stubException3->getLine() . "\n    code(integer): 0\n    stacktrace(string): " . $this->stubException3->getTraceAsString() . "\n}\n",
                            (string) $this->stubException3
        );
    }
}
?>