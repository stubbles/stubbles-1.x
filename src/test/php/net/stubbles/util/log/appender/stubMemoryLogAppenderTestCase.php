<?php
/**
 * Test for net::stubbles::util:log::appender::stubMemoryLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @version     $Id: stubMemoryLogAppenderTestCase.php 2432 2009-12-28 18:14:55Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubMemoryLogAppender');
/**
 * Test for net::stubbles::util:log::appender::stubMemoryLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @group       util_log
 * @group       util_log_appender
 */
class stubMemoryLogAppenderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMemoryLogAppender
     */
    protected $memoryLogAppender;
    /**
     * mocked logger instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockLogger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memoryLogAppender = new stubMemoryLogAppender();
        $this->mockLogger        = $this->getMock('stubLogger',
                                                  array(),
                                                  array($this->getMock('stubLogEntryFactory'))
                                   );
    }

    /**
     * assure that appended data is stored in array (one record)
     *
     * @test
     */
    public function appendWithOneDataRecord()
    {
        $logEntry = new stubLogEntry('myTestTarget', $this->mockLogger);
        $logEntry->addData('foo');
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry));
        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('myTestTarget'));
        $this->assertEquals(array('foo'), $this->memoryLogAppender->getLogEntryData('myTestTarget', 0));
        $logEntries = $this->memoryLogAppender->getLogEntries();
        $this->assertEquals(1, count($logEntries));
        $this->assertTrue(isset($logEntries['myTestTarget']));
        $this->assertEquals(1, count($logEntries['myTestTarget']));
        $this->assertSame($logEntry, $logEntries['myTestTarget'][0]);
    }

    /**
     * assure that appended data is stored in array (more than one records)
     *
     * @test
     */
    public function appendWithMoreThanOneDataRecord()
    {
        $logEntry = new stubLogEntry('myTestTarget', $this->mockLogger);
        $logEntry->addData('foo');
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry));
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry));
        $this->assertEquals(2, $this->memoryLogAppender->countLogEntries('myTestTarget'));
        $this->assertEquals(array('foo'), $this->memoryLogAppender->getLogEntryData('myTestTarget', 0));
        $this->assertEquals(array('foo'), $this->memoryLogAppender->getLogEntryData('myTestTarget', 1));
        $logEntries = $this->memoryLogAppender->getLogEntries();
        $this->assertEquals(1, count($logEntries));
        $this->assertTrue(isset($logEntries['myTestTarget']));
        $this->assertEquals(2, count($logEntries['myTestTarget']));
        $this->assertSame($logEntry, $logEntries['myTestTarget'][0]);
        $this->assertSame($logEntry, $logEntries['myTestTarget'][1]);
    }

    /**
     * assure that appended data is stored in array (more than one records)
     *
     * @test
     */
    public function appendWithMoreThanOneTargets()
    {
        $logEntry1 = new stubLogEntry('myTestTarget1', $this->mockLogger);
        $logEntry1->addData('foo');
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry1));
        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('myTestTarget1'));
        $this->assertEquals(0, $this->memoryLogAppender->countLogEntries('myTestTarget2'));
        $this->assertEquals(array('foo'), $this->memoryLogAppender->getLogEntryData('myTestTarget1', 0));
        $logEntry2 = new stubLogEntry('myTestTarget2', $this->mockLogger);
        $logEntry2->addData('bar');
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry2));
        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('myTestTarget1'));
        $this->assertEquals(1, $this->memoryLogAppender->countLogEntries('myTestTarget2'));
        $this->assertEquals(array('bar'), $this->memoryLogAppender->getLogEntryData('myTestTarget2', 0));
        $logEntries = $this->memoryLogAppender->getLogEntries();
        $this->assertEquals(2, count($logEntries));
        $this->assertTrue(isset($logEntries['myTestTarget1']));
        $this->assertEquals(1, count($logEntries['myTestTarget1']));
        $this->assertSame($logEntry1, $logEntries['myTestTarget1'][0]);
        $this->assertTrue(isset($logEntries['myTestTarget2']));
        $this->assertEquals(1, count($logEntries['myTestTarget2']));
        $this->assertSame($logEntry2, $logEntries['myTestTarget2'][0]);
        $logEntries = $this->memoryLogAppender->getLogEntries('myTestTarget1');
        $this->assertEquals(1, count($logEntries));
        $this->assertEquals(1, count($logEntries));
        $this->assertSame($logEntry1, $logEntries[0]);

        $logEntries = $this->memoryLogAppender->getLogEntries('myTestTarget2');
        $this->assertEquals(1, count($logEntries));
        $this->assertSame($logEntry2, $logEntries[0]);
    }

    /**
     * finalize() clears memory
     *
     * @test
     */
    public function finalizeClearsMemory()
    {
        $logEntry1 = new stubLogEntry('myTestTarget1', $this->mockLogger);
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry1));
        $logEntry2 = new stubLogEntry('myTestTarget2', $this->mockLogger);
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry2));
        $this->assertEquals(2, count($this->memoryLogAppender->getLogEntries()));
        $this->memoryLogAppender->finalize();
        $this->assertEquals(0, count($this->memoryLogAppender->getLogEntries()));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function countLogEntriesForNonExistingTargetReturns0()
    {
        $this->assertEquals(0, $this->memoryLogAppender->countLogEntries('myTestTarget'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function returnLogEntryDataForNonExistingTargetReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->memoryLogAppender->getLogEntryData('myTestTarget', 0));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function returnLogEntryDataForNonExistingPositionReturnsEmptyArray()
    {
        $logEntry1 = new stubLogEntry('myTestTarget', $this->mockLogger);
        $logEntry1->addData('foo');
        $this->assertSame($this->memoryLogAppender, $this->memoryLogAppender->append($logEntry1));
        $this->assertEquals(array(), $this->memoryLogAppender->getLogEntryData('myTestTarget', 1));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function returnLogEntriesForNonExistingTargetReturnsEmptyArray()
    {
        $this->assertEquals(array(), $this->memoryLogAppender->getLogEntries('myTestTarget'));
    }
}
?>