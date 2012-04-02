<?php
/**
 * Test for net::stubbles::reflection::stubReflectionExtension.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionExtensionTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionExtension');
/**
 * Test for net::stubbles::reflection::stubReflectionExtension.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionExtensionTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubReflectionExtension
     */
    protected $stubRefExtension;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        // use an extension that is always available, has classes as well as
        // functions and both at the lowest possible number
        $this->stubRefExtension = new stubReflectionExtension('date');
    }

    /**
     * assure that instances of stubReflectionExtension for the same class are equal
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->stubRefExtension->equals($this->stubRefExtension));
        $stubRefExtension1 = new stubReflectionExtension('date');
        $stubRefExtension2 = new stubReflectionExtension('standard');
        $this->assertTrue($this->stubRefExtension->equals($stubRefExtension1));
        $this->assertTrue($stubRefExtension1->equals($this->stubRefExtension));
        $this->assertFalse($this->stubRefExtension->equals($stubRefExtension2));
        $this->assertFalse($this->stubRefExtension->equals('foo'));
        $this->assertFalse($stubRefExtension2->equals($this->stubRefExtension));
    }

    /**
     * test behaviour if casted to string
     *
     * @test
     */
    public function toString()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionExtension[date] {\n}\n", (string) $this->stubRefExtension);
    }

    /**
     * test that getting the functions works correct
     *
     * @test
     */
    public function getFunctions()
    {
        $stubRefFunctions = $this->stubRefExtension->getFunctions();
        foreach ($stubRefFunctions as $stubRefFunction) {
            $this->assertInstanceOf('stubReflectionFunction', $stubRefFunction);
        }
    }

    /**
     * test that getting the classes works as expected
     *
     * @test
     */
    public function getClasses()
    {
        $stubRefClasses = $this->stubRefExtension->getClasses();
        foreach ($stubRefClasses as $stubRefClass) {
            $this->assertInstanceOf('stubReflectionClass', $stubRefClass);
        }
    }
}
?>