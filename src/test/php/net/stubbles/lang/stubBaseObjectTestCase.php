<?php
/**
 * Tests for net::stubbles::lang::stubBaseObject.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id: stubBaseObjectTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubBaseObject');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  lang_test
 */
class stub1stubBaseObject extends stubBaseObject
{
    /**
     * a property
     *
     * @var  int
     */
    protected $bar = 5;

    /**
     * returns name of the class
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'test::stub1stubBaseObject';
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  lang_test
 */
class stub2stubBaseObject extends stubBaseObject
{
    /**
     * a property
     *
     * @var  stubObject
     */
    public $stubBaseObject;
    /**
     * a property
     *
     * @var  string
     */
    private $foo = 'bar';

    /**
     * returns name of the class
     *
     * @return  string
     */
    public function getClassname()
    {
        return 'test::stub2stubBaseObject';
    }
}
/**
 * Tests for net::stubbles::lang::stubBaseObject.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubBaseObjectTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance 1 to be used for tests
     *
     * @var  stubBaseObject
     */
    protected $stubBaseObject1;
    /**
     * instance 2 to be used for tests
     *
     * @var  stubBaseObject
     */
    protected $stubBaseObject2;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->stubBaseObject1 = new stub1stubBaseObject();
        $this->stubBaseObject2 = new stub2stubBaseObject();
        $this->stubBaseObject2->stubBaseObject = $this->stubBaseObject1;
    }

    /**
     * @test
     */
    public function getClassReturnsReflectorForClass()
    {
        $refObject = $this->stubBaseObject1->getClass();
        $this->assertInstanceOf('stubReflectionObject', $refObject);
        $this->assertEquals('stub1stubBaseObject', $refObject->getName());
    }

    /**
     * assure that class name mapping works as expected
     *
     * @test
     */
    public function getPackage()
    {
        $refPackage = $this->stubBaseObject1->getPackage();
        $this->assertInstanceOf('stubReflectionPackage', $refPackage);
        $this->assertEquals('test', $refPackage->getName());
    }

    /**
     * package name should be returned
     *
     * @test
     */
    public function getPackageName()
    {
        $baseObject = new stubBaseObject();
        $this->assertEquals('net::stubbles::lang', $baseObject->getPackageName());
    }

    /**
     * assure that the equal() method works correct
     *
     * @test
     */
    public function compareWithEquals()
    {
        $this->assertTrue($this->stubBaseObject1->equals($this->stubBaseObject1));
        $this->assertTrue($this->stubBaseObject2->equals($this->stubBaseObject2));
        $this->assertFalse($this->stubBaseObject1->equals($this->stubBaseObject2));
        $this->assertFalse($this->stubBaseObject1->equals('foo'));
        $this->assertFalse($this->stubBaseObject1->equals(6));
        $this->assertFalse($this->stubBaseObject1->equals(true));
        $this->assertFalse($this->stubBaseObject1->equals(false));
        $this->assertFalse($this->stubBaseObject1->equals(null));
        $this->assertFalse($this->stubBaseObject2->equals($this->stubBaseObject1));
        $this->assertFalse($this->stubBaseObject1->equals(new stub1stubBaseObject()));
        $this->assertFalse($this->stubBaseObject2->equals(new stub2stubBaseObject()));
    }
}
?>