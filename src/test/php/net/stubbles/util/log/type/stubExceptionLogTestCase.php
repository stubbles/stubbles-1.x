<?php
/**
 * Test for net::stubbles::util::log::type::stubExceptionLog.
 *
 * @package     stubbles
 * @subpackage  util_log_type_test
 * @version     $Id: stubExceptionLogTestCase.php 2342 2009-10-06 13:02:37Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException',
                      'net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory',
                      'net::stubbles::util::log::type::stubExceptionLog'
);
/**
 * Chained exception for test purposes.
 *
 * @package     stubbles
 * @subpackage  util_log_type_test
 */
class TestExceptionLogException extends stubChainedException
{
    /**
     * returns class name
     *
     * @return  string
     */
    public function getClassName()
    {
        return 'net::stubbles::util::log::type::test::TestExceptionLogException';
    }
}
/**
 * Test for net::stubbles::util::log::type::stubExceptionLog.
 *
 * @package     stubbles
 * @subpackage  util_log_type_test
 * @group       util_log
 * @group       util_log_type
 */
class stubExceptionLogTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubExceptionLog
     */
    protected $exceptionLog;
    /**
     * log appender to collect logged data
     *
     * @var  stubMemoryLogAppender
     */
    protected $memoryLogAppender;
    /**
     * logger to log to
     *
     * @var  stubLogger
     */
    protected $logger;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->logger       = new stubLogger(new stubEmptyLogEntryFactory());
        $this->logAppender  = new stubMemoryLogAppender();
        $this->logger->addLogAppender($this->logAppender);
        $this->exceptionLog = stubExceptionLog::create($this->logger);
    }

    /**
     * assure that the exception is logged
     *
     * @test
     */
    public function logException()
    {
        $this->exceptionLog->log(new Exception('exception message'));
        $line = __LINE__ - 1;
        $logEntries = $this->logAppender->getLogEntries();
        $this->assertEquals(1, count($logEntries));
        $this->assertEquals(1, count($logEntries['exceptions']));
        $this->assertEquals('exceptions', $logEntries['exceptions'][0]->getTarget());
        $logData = explode(stubLogEntry::DEFAULT_SEPERATOR, $logEntries['exceptions'][0]->get());
        $this->assertEquals('Exception', $logData[0]);
        $this->assertEquals('exception message', $logData[1]);
        $this->assertEquals(__FILE__, $logData[2]);
        $this->assertEquals($line, $logData[3]);
        $this->assertEquals('', $logData[4]);
        $this->assertEquals('', $logData[5]);
        $this->assertEquals('', $logData[6]);
        $this->assertEquals('', $logData[7]);
    }

    /**
     * assure that the exception is logged
     *
     * @test
     */
    public function logChainedException()
    {
        $exception = new TestExceptionLogException('chained exception', new Exception('exception message'));
        $line = __LINE__ - 1;
        $this->exceptionLog->setLogTarget('foo');
        $this->exceptionLog->log($exception);
        $logEntries = $this->logAppender->getLogEntries();
        $this->assertEquals(1, count($logEntries));
        $this->assertEquals(1, count($logEntries['foo']));
        $this->assertEquals('foo', $logEntries['foo'][0]->getTarget());
        $logData = explode(stubLogEntry::DEFAULT_SEPERATOR, $logEntries['foo'][0]->get());
        $this->assertEquals('net::stubbles::util::log::type::test::TestExceptionLogException', $logData[0]);
        $this->assertEquals('chained exception', $logData[1]);
        $this->assertEquals(__FILE__, $logData[2]);
        $this->assertEquals($line, $logData[3]);
        $this->assertEquals('Exception', $logData[4]);
        $this->assertEquals('exception message', $logData[5]);
        $this->assertEquals(__FILE__, $logData[6]);
        $this->assertEquals($line, $logData[7]);
    }

    /**
     * assure that the exception is logged
     *
     * @test
     */
    public function logChainedExceptionWithoutChainedException()
    {
        $exception = new TestExceptionLogException('chained exception');
        $line      = __LINE__ - 1;
        $this->exceptionLog->setLogTarget('foo');
        $this->exceptionLog->log($exception);
        $logEntries = $this->logAppender->getLogEntries();
        $this->assertEquals(1, count($logEntries));
        $this->assertEquals(1, count($logEntries['foo']));
        $this->assertEquals('foo', $logEntries['foo'][0]->getTarget());
        $logData = explode(stubLogEntry::DEFAULT_SEPERATOR, $logEntries['foo'][0]->get());
        $this->assertEquals('net::stubbles::util::log::type::test::TestExceptionLogException', $logData[0]);
        $this->assertEquals('chained exception', $logData[1]);
        $this->assertEquals(__FILE__, $logData[2]);
        $this->assertEquals($line, $logData[3]);
        $this->assertEquals('', $logData[4]);
        $this->assertEquals('', $logData[5]);
        $this->assertEquals('', $logData[6]);
        $this->assertEquals('', $logData[7]);
    }
}
?>