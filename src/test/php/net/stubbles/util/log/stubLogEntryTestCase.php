<?php
/**
 * Test for net::stubbles::util::log::stubLogEntry.
 *
 * @package     stubbles
 * @subpackage  util_log_test
 * @version     $Id: stubLogEntryTestCase.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::stubLogEntry');
/**
 * Test for net::stubbles::util::log::stubLogEntry.
 *
 * @package     stubbles
 * @subpackage  util_log_test
 * @group       util_log
 */
class stubLogEntryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLogEntry
     */
    protected $logEntry;
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
        $this->mockLogger = $this->getMock('stubLogger',
                                           array(),
                                           array($this->getMock('stubLogEntryFactory'))
                            );
        $this->logEntry   = new stubLogEntry('testTarget', $this->mockLogger);
    }

    /**
     * target is set
     *
     * @test
     */
    public function targetIsSet()
    {
        $this->assertEquals('testTarget', $this->logEntry->getTarget());
    }

    /**
     * log data is initially empty
     *
     * @test
     */
    public function logDataIsInitiallyEmpty()
    {
        $this->assertEquals('', $this->logEntry->get());
    }

    /**
     * @test
     */
    public function logCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntry);
        $this->logEntry->log();
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function logDelayedCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('logDelayed')
                         ->with($this->logEntry);
        $this->logEntry->logDelayed();
    }

    /**
     * @test
     */
    public function addDataRemovesCarriageReturnAndEscapesLineBreaks()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData("fo\ro"));
        $this->assertSame($this->logEntry, $this->logEntry->addData("ba\nr"));
        $this->assertSame($this->logEntry, $this->logEntry->addData("ba\r\nz"));
        $this->assertEquals('foo' . stubLogEntry::DEFAULT_SEPERATOR  . 'ba<nl>r' . stubLogEntry::DEFAULT_SEPERATOR  . 'ba<nl>z',
                            $this->logEntry->get()
        );
        $this->assertEquals(array('foo',
                                  'ba<nl>r',
                                  'ba<nl>z'
                            ),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     */
    public function addDataRemovesSeperator()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('foo' . stubLogEntry::DEFAULT_SEPERATOR . 'bar;baz'));
        $this->assertEquals('foobar;baz', $this->logEntry->get());
        $this->assertSame($this->logEntry, $this->logEntry->setSeperator(';'));
        $this->assertEquals('foo' . stubLogEntry::DEFAULT_SEPERATOR . 'barbaz', $this->logEntry->get());
        $this->assertSame($this->logEntry, $this->logEntry->setSeperator(stubLogEntry::DEFAULT_SEPERATOR));
        $this->assertEquals('foobar;baz', $this->logEntry->get());
        $this->assertEquals(array('foobar;baz'),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     */
    public function addDataChangesSingleDoubleQuoteToEmptyString()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('"'));
        $this->assertEquals('', $this->logEntry->get());
        $this->assertEquals(array(''),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     */
    public function addDataAppendsDoubleQuoteToStringIfStringStartsWithDoubleQuoteButDoesNotEndWithDoubleQuote()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('foo"bar'));
        $this->assertSame($this->logEntry, $this->logEntry->addData('"bar'));
        $this->assertSame($this->logEntry, $this->logEntry->addData('"baz"'));
        $this->assertEquals('foo"bar' . stubLogEntry::DEFAULT_SEPERATOR . '"bar"' . stubLogEntry::DEFAULT_SEPERATOR . '"baz"',
                            $this->logEntry->get()
        );
        $this->assertEquals(array('foo"bar',
                                  '"bar"',
                                  '"baz"'
                            ),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function replaceDataReturnsSilentIfGivenPositionDoesNotExist()
    {
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(0, "foo"));
        $this->assertEquals(array(),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function replaceDataRemovesCarriageReturnAndEscapesLineBreaks()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData("test1"));
        $this->assertSame($this->logEntry, $this->logEntry->addData("test2"));
        $this->assertSame($this->logEntry, $this->logEntry->addData("test3"));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(0, "fo\ro"));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(1, "ba\nr"));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(2, "ba\r\nz"));
        $this->assertEquals('foo' . stubLogEntry::DEFAULT_SEPERATOR  . 'ba<nl>r' . stubLogEntry::DEFAULT_SEPERATOR  . 'ba<nl>z',
                            $this->logEntry->get()
        );
        $this->assertEquals(array('foo',
                                  'ba<nl>r',
                                  'ba<nl>z'
                            ),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function replaceDataRemovesSeperator()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('test'));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(0, 'foo' . stubLogEntry::DEFAULT_SEPERATOR . 'bar;baz'));
        $this->assertEquals('foobar;baz', $this->logEntry->get());
        $this->assertSame($this->logEntry, $this->logEntry->setSeperator(';'));
        $this->assertEquals('foo' . stubLogEntry::DEFAULT_SEPERATOR . 'barbaz', $this->logEntry->get());
        $this->assertSame($this->logEntry, $this->logEntry->setSeperator(stubLogEntry::DEFAULT_SEPERATOR));
        $this->assertEquals('foobar;baz', $this->logEntry->get());
        $this->assertEquals(array('foobar;baz'),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function replaceDataChangesSingleDoubleQuoteToEmptyString()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('test'));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(0, '"'));
        $this->assertEquals('', $this->logEntry->get());
        $this->assertEquals(array(''),
                            $this->logEntry->getData()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function replaceDataAppendsDoubleQuoteToStringIfStringStartsWithDoubleQuoteButDoesNotEndWithDoubleQuote()
    {
        $this->assertSame($this->logEntry, $this->logEntry->addData('test1'));
        $this->assertSame($this->logEntry, $this->logEntry->addData('test2'));
        $this->assertSame($this->logEntry, $this->logEntry->addData('test3'));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(0, 'foo"bar'));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(1, '"bar'));
        $this->assertSame($this->logEntry, $this->logEntry->replaceData(2, '"baz"'));
        $this->assertEquals('foo"bar' . stubLogEntry::DEFAULT_SEPERATOR . '"bar"' . stubLogEntry::DEFAULT_SEPERATOR . '"baz"',
                            $this->logEntry->get()
        );
        $this->assertEquals(array('foo"bar',
                                  '"bar"',
                                  '"baz"'
                            ),
                            $this->logEntry->getData()
        );
    }
}
?>