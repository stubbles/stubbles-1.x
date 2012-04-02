<?php
/**
 * Test for net::stubbles::util::log::appender::stubMailLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @version     $Id: stubMailLogAppenderTestCase.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubMailLogAppender');
/**
 * Test for net::stubbles::util::log::appender::stubMailLogAppender.
 *
 * @package     stubbles
 * @subpackage  util_log_appender_test
 * @group       util_log
 * @group       util_log_appender
 */
class stubMailLogAppenderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMailLogAppender
     */
    protected $mailLogAppender;
    /**
     * log entry instance
     *
     * @var stubLogEntry
     */
    protected $logEntry1;
    /**
     * log entry instance
     *
     * @var stubLogEntry
     */
    protected $logEntry2;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mailLogAppender   = $this->getMock('stubMailLogAppender',
                                                  array('sendMail'),
                                                  array('test@example.org')
                                   );
        $_SERVER['HTTP_HOST']    = 'example.org';
        $_SERVER['PHP_SELF']     = '/example.php';
        $_SERVER['QUERY_STRING'] = 'example=dummy';
        $this->logEntry1         = new stubLogEntry('foo',
                                                    $this->getMock('stubLogger',
                                                                   array(),
                                                                   array($this->getMock('stubLogEntryFactory'))
                                                    )
                                   );
        $this->logEntry2         = new stubLogEntry('blub',
                                                    $this->getMock('stubLogger',
                                                                   array(),
                                                                   array($this->getMock('stubLogEntryFactory'))
                                                    )
                                   );
    }

    /**
     * property handling
     *
     * @test
     */
    public function propertyHandling()
    {
        $mailLogAppender = new stubMailLogAppender('test@example.org');
        $this->assertEquals('test@example.org', $mailLogAppender->getMailAddress());
        $this->assertEquals('stubDebugger', $mailLogAppender->getSenderName());
        
        $mailLogAppender = new stubMailLogAppender('test@example.org', 'TestDebugger');
        $this->assertEquals('test@example.org', $mailLogAppender->getMailAddress());
        $this->assertEquals('TestDebugger', $mailLogAppender->getSenderName());
    }

    /**
     * assure that no log entries leads to no mail
     *
     * @test
     */
    public function testFinalizeWithoutLogEntries()
    {
        $this->mailLogAppender->expects($this->never())
                              ->method('sendMail');
        $this->mailLogAppender->finalize();
    }

    /**
     * assure that log data will be sent via mail
     *
     * @test
     */
    public function finalizeWithLogEntries()
    {
        if (isset($_SERVER['HTTP_REFERER']) == true) {
            unset($_SERVER['HTTP_REFERER']);
        }
        
        $this->mailLogAppender->expects($this->once())
                              ->method('sendMail')
                              ->with($this->equalTo('Debug message from example.org'), $this->equalTo("foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n"));
        $this->logEntry1->addData('bar')
                        ->addData('baz');
        $this->assertSame($this->mailLogAppender, $this->mailLogAppender->append($this->logEntry1));
        $this->logEntry2->addData('shit')
                        ->addData('happens');
        $this->assertSame($this->mailLogAppender, $this->mailLogAppender->append($this->logEntry2));
        $this->mailLogAppender->finalize();
    }

    /**
     * assure that log data will be sent via mail
     *
     * @test
     */
    public function finalizeWithLogEntriesAndReferer()
    {
        $_SERVER['HTTP_REFERER'] = 'referer';
        $this->mailLogAppender->expects($this->once())
                              ->method('sendMail')
                              ->with($this->equalTo('Debug message from example.org'), $this->equalTo("foo: bar|baz\n\nblub: shit|happens\n\n\nURL that caused this:\nhttp://example.org/example.php?example=dummy\n\nReferer:\nreferer\n"));
        $this->logEntry1->addData('bar')
                        ->addData('baz');
        $this->assertSame($this->mailLogAppender, $this->mailLogAppender->append($this->logEntry1));
        $this->logEntry2->addData('shit')
                        ->addData('happens');
        $this->assertSame($this->mailLogAppender, $this->mailLogAppender->append($this->logEntry2));
        $this->mailLogAppender->finalize();
    }
}
?>