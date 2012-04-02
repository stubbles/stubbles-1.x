<?php
/**
 * Test for net::stubbles::util::log::ioc::stubBaseLoggerProvider.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @version     $Id: stubBaseLoggerProviderTestCase.php 2060 2009-01-26 12:57:25Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::ioc::stubBaseLoggerProvider');
/**
 * Test for net::stubbles::util::log::ioc::stubBaseLoggerProvider.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @group       util_log
 * @group       util_log_ioc
 */
class stubBaseLoggerProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubBaseLoggerProvider
     */
    protected $baseLoggerProvider;
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
        $this->baseLoggerProvider  = new stubBaseLoggerProvider($this->mockLogEntryFactory);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresent()
    {
        $this->assertTrue($this->baseLoggerProvider->getClass()->getConstructor()->hasAnnotation('Inject'));
    }

    /**
     * createLogEntry() uses LogEntryFactory
     *
     * @test
     */
    public function createdLoggerUsesLogEntryFactory()
    {
        $logger   = $this->baseLoggerProvider->get();
        $logEntry = new stubLogEntry('testTarget', $logger);
        $this->mockLogEntryFactory->expects($this->once())
                                  ->method('create')
                                  ->with($this->equalTo('testTarget'), $this->equalTo($logger))
                                  ->will($this->returnValue($logEntry));
        $this->assertSame($logEntry, $logger->createLogEntry('testTarget'));
    }
}
?>