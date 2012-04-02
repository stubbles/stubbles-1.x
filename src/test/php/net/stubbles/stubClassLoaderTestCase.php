<?php
/**
 * Tests for net::stubbles::stubClassLoader.
 *
 * @package     stubbles
 * @subpackage  test
 * @version     $Id: stubClassLoaderTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Tests for net::stubbles::stubClassLoader.
 *
 * @package     stubbles
 * @subpackage  test
 * @group       lang
 */
class stubClassLoaderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * assure that class name mapping works as expected
     *
     * @test
     */
    public function classNames()
    {
        $this->assertEquals('Bar', stubClassLoader::getNonQualifiedClassName('example::foo::Bar'));
        $this->assertEquals('example::foo', stubClassLoader::getPackageName('example::foo::Bar'));
        $this->assertNull(stubClassLoader::getFullQualifiedClassName('Bar'));
        $this->assertEquals('', stubClassLoader::getPackageName('Bar'));
        $this->assertEquals('stubClassLoader', stubClassLoader::getNonQualifiedClassName('stubClassLoader'));
    }

    /**
     * assert that __static is called, but only once
     *
     * @test
     */
    public function staticLoading()
    {
        $this->assertFalse(class_exists('WithStatic', false));
        stubClassLoader::load('org::stubbles::test::WithStatic');
        $this->assertEquals(1, WithStatic::getCalled());
        $this->assertTrue(class_exists('WithStatic', false));
        $this->assertEquals('org::stubbles::test::WithStatic', stubClassLoader::getFullQualifiedClassName('WithStatic'));
        $this->assertEquals('WithStatic', stubClassLoader::getNonQualifiedClassName('org::stubbles::test::WithStatic'));
        stubClassLoader::load('org::stubbles:test::WithStatic');
        $this->assertEquals(1, WithStatic::getCalled());
    }

    /**
     * assert that a stubClassNotFoundException is thrown in case a class can not be loaded
     *
     * @test
     */
    public function classNotFound()
    {
        try {
            @stubClassLoader::load('org::stubbles::test::DoesNotExist');
        } catch (stubClassNotFoundException $cnfe) {
            $this->assertEquals('org::stubbles::test::DoesNotExist', $cnfe->getNotFoundClassName());
            $this->assertEquals("net::stubbles::stubClassNotFoundException {\n"
                              . '    message(string): The class org::stubbles::test::DoesNotExist loaded in ' . __FILE__ . ' on line ' . (__LINE__ - 4) . " was not found.\n"
                              . "    classname(string): org::stubbles::test::DoesNotExist\n"
                              . '    file(string): ' . $cnfe->getFile() . "\n"
                              . '    line(integer): ' . $cnfe->getLine() . "\n"
                              . '    code(integer): ' . 0 . "\n}\n",
                                (string) $cnfe);
            return;
        }
        
        $this->fail('Expected stubClassNotFoundException, got nothing or another exception.');
    }

    /**
     * loading nothing does not do any harm
     *
     * @test
     */
    public function loadNothing()
    {
        stubClassLoader::load();
    }
}
?>