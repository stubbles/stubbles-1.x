<?php
/**
 * Test for net::stubbles::util::log::entryfactory::stubDefaultLogEntryFactory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory_test
 * @version     $Id: stubDefaultLogEntryFactoryTestCase.php 2692 2010-08-30 09:48:20Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::entryfactory::stubDefaultLogEntryFactory');
/**
 * Test for net::stubbles::util::log::entryfactory::stubDefaultLogEntryFactory.
 *
 * @package     stubbles
 * @subpackage  util_log_entryfactory_test
 * @group       util_log
 * @group       util_log_entryfactory
 */
class stubDefaultLogEntryFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultLogEntryFactory
     */
    protected $defaultLogEntryFactory;
    /**
     * created instance without session
     *
     * @var  stubLogEntry
     */
    protected $logEntryWithoutSession;
    /**
     * created instance with session
     *
     * @var  stubLogEntry
     */
    protected $logEntryWithSession;
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
        $this->mockLogger             = $this->getMock('stubLogger',
                                                       array(),
                                                       array($this->getMock('stubLogEntryFactory'))
                                        );
        $this->defaultLogEntryFactory = new stubDefaultLogEntryFactory();
        $this->logEntryWithoutSession = $this->defaultLogEntryFactory->create('testTarget', $this->mockLogger);
        $mockSession                  = $this->getMock('stubSession');
        $mockSession->expects($this->once())
                    ->method('getId')
                    ->will($this->returnValue(313));
        $this->defaultLogEntryFactory->setSession($mockSession);
        $this->logEntryWithSession = $this->defaultLogEntryFactory->create('testTarget', $this->mockLogger);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresent()
    {
        $refMethod = new stubReflectionMethod('stubDefaultLogEntryFactory', 'setSession');
        $this->assertTrue($refMethod->hasAnnotation('Inject'));
        $this->assertTrue($refMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * target is set
     *
     * @test
     */
    public function createdLogEntryHasCorrectTarget()
    {
        $this->assertEquals('testTarget', $this->logEntryWithoutSession->getTarget());
        $this->assertEquals('testTarget', $this->logEntryWithSession->getTarget());
    }

    /**
     * @test
     */
    public function createdLogEntryWithoutSessionContainsTime()
    {
        $currentTime = time();
        $loggedTime  = strtotime($this->logEntryWithoutSession->get());
        $this->assertGreaterThan($currentTime -2, $loggedTime);
        $this->assertLessThan($currentTime +2, $loggedTime);
    }

    /**
     * @test
     */
    public function createdLogEntryWithSessionContainsTimeAndSessionId()
    {
        $logData     = explode(stubLogEntry::DEFAULT_SEPERATOR, $this->logEntryWithSession->get());
        $currentTime = time();
        $loggedTime  = strtotime($logData[0]);
        $this->assertGreaterThan($currentTime -2, $loggedTime);
        $this->assertLessThan($currentTime +2, $loggedTime);
        $this->assertEquals('313', $logData[1]);
    }

    /**
     * log() calls given logger
     *
     * @test
     */
    public function createdLogEntryWithoutSessionCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntryWithoutSession);
        $this->logEntryWithoutSession->log();
    }

    /**
     * log() calls given logger
     *
     * @test
     */
    public function createdLogEntryWithSessionCallsGivenLogger()
    {
        $this->mockLogger->expects($this->once())
                         ->method('log')
                         ->with($this->logEntryWithSession);
        $this->logEntryWithSession->log();
    }

    /**
     * @test
     */
    public function recreateOnlyReturnsGivenLogEntryUnmodified()
    {
        $this->assertSame($this->logEntryWithoutSession,
                          $this->defaultLogEntryFactory->recreate($this->logEntryWithoutSession,
                                                                  $this->mockLogger
                                                         )
        );
        $this->assertSame($this->logEntryWithSession,
                          $this->defaultLogEntryFactory->recreate($this->logEntryWithSession,
                                                                  $this->mockLogger
                                                         )
        );
    }
}
?>