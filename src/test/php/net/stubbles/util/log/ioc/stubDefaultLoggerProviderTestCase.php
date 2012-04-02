<?php
/**
 * Test for net::stubbles::util::log::ioc::stubDefaultLoggerProvider.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @version     $Id: stubDefaultLoggerProviderTestCase.php 3230 2011-11-23 17:04:19Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::ioc::stubDefaultLoggerProvider');
/**
 * Test for net::stubbles::util::log::ioc::stubDefaultLoggerProvider.
 *
 * @package     stubbles
 * @subpackage  util_log_ioc_test
 * @group       util_log
 * @group       util_log_ioc
 */
class stubDefaultLoggerProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultLoggerProvider
     */
    protected $defaultLoggerProvider;
    /**
     * mocked logger instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockLogger;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockLogger             = $this->getMock('stubLogger',
                                                       array(),
                                                       array($this->getMock('stubLogEntryFactory')
                                                       )
                                        );
        $this->defaultLoggerProvider  = new stubDefaultLoggerProvider($this->mockLogger, dirname(__FILE__));
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationPresent()
    {
        $constructor = $this->defaultLoggerProvider->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $refParams = $constructor->getParameters();
        $this->assertTrue($refParams[0]->hasAnnotation('Named'));
        $this->assertEquals('util.log.baseLogger', $refParams[0]->getAnnotation('Named')->getName());
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.log.path', $refParams[1]->getAnnotation('Named')->getName());
    }

    /**
     * provider returns always the same instance
     *
     * @test
     */
    public function returnsAlwaysSameInstance()
    {
        $this->mockLogger->expects($this->exactly(3))
                         ->method('hasLogAppenders')
                         ->will($this->onConsecutiveCalls(false, true, true));
        $this->mockLogger->expects($this->once())
                         ->method('addLogAppender');
        $this->assertSame($this->mockLogger, $this->defaultLoggerProvider->get());
        $this->assertSame($this->defaultLoggerProvider->get(), $this->defaultLoggerProvider->get());
    }

    /**
     * logger get a file log appender if it does not have any log appenders yet
     *
     * @test
     */
    public function addsFileLogAppenderIfLoggerHasNoAppenders()
    {
        $defaultLoggerProvider = new stubDefaultLoggerProvider(new stubLogger($this->getMock('stubLogEntryFactory')),
                                                               dirname(__FILE__)
                                 );
        $logger                = $defaultLoggerProvider->get();
        $this->assertTrue($logger->hasLogAppenders());
        $logAppender = $logger->getLogAppenders();
        $this->assertEquals(1, count($logAppender));
        $this->assertInstanceOf('stubFileLogAppender', $logAppender[0]);
        $this->assertSame($logger, $defaultLoggerProvider->get());
        $this->assertTrue($logger->hasLogAppenders());
        $logAppender = $logger->getLogAppenders();
        $this->assertEquals(1, count($logAppender));
        $this->assertInstanceOf('stubFileLogAppender', $logAppender[0]);
    }
}
?>