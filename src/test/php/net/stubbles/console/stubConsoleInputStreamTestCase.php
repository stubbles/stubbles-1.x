<?php
/**
 * Test for net::stubbles::console::stubConsoleInputStream.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @version     $Id: stubConsoleInputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleInputStream');
/**
 * Test for net::stubbles::console::stubConsoleInputStream.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @group       console
 */
class stubConsoleInputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * console input stream is always the same instance
     *
     * @test
     */
    public function sameInstance()
    {
        $in1 = stubConsoleInputStream::forIn();
        $in2 = stubConsoleInputStream::forIn();
        $this->assertSame($in1, $in2);
    }
}
?>