<?php
/**
 * Test for net::stubbles::util::log::stubLogger.
 *
 * @package     stubbles
 * @subpackage  util_log_test
 * @version     $Id: stubLoggerTestCase.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogger',
                      'net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  util_log_test
 */
class NonClearingstubMemoryLogAppender extends stubMemoryLogAppender
{
    /**
     * finalize the log target
     */
    public function finalize()
    {
        // intentionally empty
    }
}
/**
 * Test for net::stubbles::util::log::stubLogger.
 *
 * @package     stubbles
 * @subpackage  util_log_test
 * @group       util_log
 */
class stubLoggerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLogger
     */
    protected $logger;
    /**
     * mocked log entry factory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockLogEntryFactory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogEntryFactory = $this->getMock('stubLogEntryFactory');
        $this->logger              = new stubLogger($this->mockLogEntryFactory);
    }

    /**
     * initial instance does not have any log appenders
     *
     * @test
     */
    public function initialInstanceHasNoLogAppenders()
    {
        $this->assertFalse($this->logger->hasLogAppenders());
        $this->assertEquals(array(), $this->logger->getLogAppenders());
    }

    /**
     * more than one log appender can be added
     *
     * @test
     */
    public function moreThanOneLogAppenderCanBeAdded()
    {
        $mockLogAppender1 = $this->getMock('stubLogAppender');
        $this->logger->addLogAppender($mockLogAppender1);
        $this->assertTrue($this->logger->hasLogAppenders());
        $this->assertEquals(array($mockLogAppender1),
                            $this->logger->getLogAppenders()
        );

        $mockLogAppender2 = $this->getMock('stubLogAppender');
        $this->logger->addLogAppender($mockLogAppender2);
        $this->assertTrue($this->logger->hasLogAppenders());
        $this->assertEquals(array($mockLogAppender1,
                                  $mockLogAppender2
                            ),
                            $this->logger->getLogAppenders()
        );

    }

    /**
     * a destructed logger loops through all log appenders and calls their finalize() method
     *
     * @test
     */
    public function destructedLoggerInstanceCallsFinalizeOnAppenders()
    {
        $logger           = new stubLogger($this->mockLogEntryFactory);
        $mockLogAppender1 = $this->getMock('stubLogAppender');
        $mockLogAppender1->expects($this->once())
                         ->method('finalize');
        $logger->addLogAppender($mockLogAppender1);
        $mockLogAppender2 = $this->getMock('stubLogAppender');
        $mockLogAppender2->expects($this->once())
                         ->method('finalize');
        $logger->addLogAppender($mockLogAppender2);
        $logger = null;
    }

    /**
     * createLogEntry() uses LogEntryFactory
     *
     * @test
     */
    public function createLogEntryUsesLogEntryFactory()
    {
        $logEntry = new stubLogEntry('testTarget', $this->logger);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('create')
                                  ->with($this->equalTo('testTarget'), $this->equalTo($this->logger))
                                  ->will($this->returnValue($logEntry));
        $this->assertSame($logEntry, $this->logger->createLogEntry('testTarget'));
    }

    /**
     * test the instance logging method
     *
     * @test
     */
    public function logAppendsLogEntryToAllLogAppender()
    {
        $logEntry = new stubLogEntry('testTarget', $this->logger);
        $mockLogAppender1 = $this->getMock('stubLogAppender');
        $mockLogAppender1->expects($this->once())->method('append')->with($this->equalTo($logEntry));
        $mockLogAppender2 = $this->getMock('stubLogAppender');
        $mockLogAppender2->expects($this->once())->method('append')->with($this->equalTo($logEntry));
        $this->logger->addLogAppender($mockLogAppender1);
        $this->logger->addLogAppender($mockLogAppender2);
        $this->logger->log($logEntry);
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function processDelayedLogEntriesWithoutDelayedLogEntriesReturn0()
    {
        $this->assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
        $this->assertEquals(0, $this->logger->processDelayedLogEntries());
        $this->assertFalse($this->logger->hasUnprocessedDelayedLogEntries());
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function processDelayedLogEntriesReturnsAmountOfProcessedLogEntries()
    {
        $this->logger = new stubLogger(new stubEmptyLogEntryFactory());
        $logEntry = new stubLogEntry('testTarget', $this->logger);
        $logEntryFactory = new stubEmptyLogEntryFactory();
        $logAppender1 = new stubMemoryLogAppender();
        $logAppender2 = new stubMemoryLogAppender();
        $this->logger->addLogAppender($logAppender1);
        $this->logger->addLogAppender($logAppender2);
        $this->logger->logDelayed($logEntry);
        $this->assertTrue($this->logger->hasUnprocessedDelayedLogEntries());
        $this->assertEquals(1, $this->logger->processDelayedLogEntries());
        $this->assertFalse($this->logger->hasUnprocessedDelayedLogEntries());

        $this->assertEquals(array($logEntry), $logAppender1->getLogEntries('testTarget'));
        $this->assertEquals(array($logEntry), $logAppender2->getLogEntries('testTarget'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function destructedLoggerInstanceProcessedDelayedLogEntries()
    {
        $logEntry     = new stubLogEntry('testTarget', $this->logger);
        $logEntry->addData('foo');
        $logAppender1 = new NonClearingstubMemoryLogAppender();
        $logAppender2 = new NonClearingstubMemoryLogAppender();
        $this->destructionHelper($logEntry, $logAppender1, $logAppender2);
        $this->assertEquals(array('foo'), $logAppender1->getLogEntryData('testTarget', 0));
        $this->assertEquals(array('foo'), $logAppender2->getLogEntryData('testTarget', 0));
    }

    /**
     * helper method for test destructedLoggerInstanceProcessedDelayedLogEntries()
     *
     * @param  stubLogEntry     $logEntry
     * @param  stubLogAppender  $logAppender1
     * @param  stubLogAppender  $logAppender2
     */
    protected function destructionHelper(stubLogEntry $logEntry, stubLogAppender $logAppender1, stubLogAppender $logAppender2)
    {
        $logger = new stubLogger(new stubEmptyLogEntryFactory());
        $logger->addLogAppender($logAppender1);
        $logger->addLogAppender($logAppender2);
        $logger->logDelayed($logEntry);
        $logger = null;
    }
}
?>