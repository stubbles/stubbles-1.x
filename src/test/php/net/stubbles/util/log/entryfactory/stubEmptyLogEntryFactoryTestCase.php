<?php
/**
 * Test for net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory_test
 * @version     $Id: stubEmptyLogEntryFactoryTestCase.php 2438 2010-01-07 16:28:49Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory');
/**
 * Test for net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory_test
 * @group       util_log
 * @group       util_log_entryfactory
 */
class stubEmptyLogEntryFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubEmptyLogEntryFactory
     */
    protected $emptyLogEntryFactory;
    /**
     * created instance
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
        $this->mockLogger           = $this->getMock('stubLogger',
                                                     array(),
                                                     array($this->getMock('stubLogEntryFactory'))
                                      );
        $this->emptyLogEntryFactory = new stubEmptyLogEntryFactory();
        $this->logEntry             = $this->emptyLogEntryFactory->create('testTarget', $this->mockLogger);
    }

    /**
     * target is set
     *
     * @test
     */
    public function createdLogEntryHasCorrectTarget()
    {
        $this->assertEquals('testTarget', $this->logEntry->getTarget());
    }

    /**
     * log data is initially empty
     *
     * @test
     */
    public function createdLogEntryIsEmpty()
    {
        $this->assertEquals('', $this->logEntry->get());
    }

    /**
     * log() calls given logger
     *
     * @test
     */
    public function createdLogEntryCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntry);
        $this->logEntry->log();
    }

    /**
     * @test
     */
    public function recreateOnlyReturnsGivenLogEntryUnmodified()
    {
        $this->assertSame($this->logEntry,
                          $this->emptyLogEntryFactory->recreate($this->logEntry,
                                                                $this->mockLogger
                                                       )
        );
    }
}
?>