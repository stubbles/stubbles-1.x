<?php
/**
 * Test for net::stubbles::util::log::appender::stubFileLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @version     $Id: stubFileLogAppenderTestCase.php 3230 2011-11-23 17:04:19Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubFileLogAppender');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::util::log::appender::stubFileLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @group       util_log
 * @group       util_log_appender
 */
class stubFileLogAppenderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFileLogAppender
     */
    protected $fileLogAppender;
    /**
     * the logfile
     *
     * @var  string
     */
    protected $logFile;
    /**
     * logfile directory
     *
     * @var  vfsStreamDirectory
     */
    protected $root;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http//vfs.bovigo.org/');
        }

        $this->root            = vfsStream::setup();
        $this->logFile         = vfsStream::url('root/test/foo-' . date('Y-m-d') . '.log');
        $this->fileLogAppender = new stubFileLogAppender(vfsStream::url('root/test'));
    }

    /**
     * creates log entry
     *
     * @return  stubLogEntry
     */
    protected function createLogEntry()
    {
        $logEntry = new stubLogEntry('foo',
                                     $this->getMock('stubLogger',
                                                    array(),
                                                    array(),
                                                    '',
                                                    false
                                     )
                    );
        $logEntry->addData('bar')
                 ->addData('baz');
        return $logEntry;
    }

    /**
     * @test
     */
    public function appendWritesLogEntryToLogfile()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->append($this->createLogEntry())
                                                ->append($this->createLogEntry())
        );
        $this->assertTrue(file_exists($this->logFile));
        $this->assertEquals("bar|baz\nbar|baz\n", file_get_contents($this->logFile));
    }

    /**
     * @test
     */
    public function createsNonExistingDirectoryWithDefaultFilemode()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->append($this->createLogEntry())
        );
        $this->assertEquals(0700, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createsNonExistingDirectoryWithOtherFilemode()
    {
        $this->assertSame($this->fileLogAppender,
                          $this->fileLogAppender->setMode(0644)
                                                ->append($this->createLogEntry())
        );
        $this->assertEquals(0644, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function finalizeIsNoOp()
    {
        $this->fileLogAppender->finalize();
    }
}
?>