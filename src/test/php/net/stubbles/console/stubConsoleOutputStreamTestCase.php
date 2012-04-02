<?php
/**
 * Test for net::stubbles::console::stubConsoleOutputStream.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @version     $Id: stubConsoleOutputStreamTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleOutputStream');
/**
 * Test for net::stubbles::console::stubConsoleOutputStream.
 *
 * @package     stubbles
 * @subpackage  console_test
 * @group       console
 */
class stubConsoleOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * console output stream is always the same instance
     *
     * @test
     */
    public function sameOutInstance()
    {
        $out1 = stubConsoleOutputStream::forOut();
        $out2 = stubConsoleOutputStream::forOut();
        $this->assertSame($out1, $out2);
    }

    /**
     * console error stream is always the same instance
     *
     * @test
     */
    public function sameErrInstance()
    {
        $err1 = stubConsoleOutputStream::forError();
        $err2 = stubConsoleOutputStream::forError();
        $this->assertSame($err1, $err2);
    }
}
?>