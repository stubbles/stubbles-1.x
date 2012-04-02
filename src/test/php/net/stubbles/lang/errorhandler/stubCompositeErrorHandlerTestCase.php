<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubCompositeErrorHandler
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubCompositeErrorHandlerTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubCompositeErrorHandler');
/**
 * Tests for net::stubbles::lang::errorhandler::stubCompositeErrorHandler
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubCompositeErrorHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCompositeErrorHandler
     */
    protected $compositeErrorHandler;
    /**
     * a mocked error handler
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler1;
    /**
     * a mocked error handler
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler2;
    /**
     * a mocked error handler
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockErrorHandler3;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->compositeErrorHandler = new stubCompositeErrorHandler();
        $this->mockErrorHandler1     = $this->getMock('stubErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler1);
        $this->mockErrorHandler2     = $this->getMock('stubErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler2);
        $this->mockErrorHandler3     = $this->getMock('stubErrorHandler');
        $this->compositeErrorHandler->addErrorHandler($this->mockErrorHandler3);
    }

    /**
     * assert that all registered handlers are returned
     *
     * @test
     */
    public function getHandlers()
    {
        $this->assertEquals(array($this->mockErrorHandler1,
                                  $this->mockErrorHandler2,
                                  $this->mockErrorHandler3
                            ),
                            $this->compositeErrorHandler->getErrorHandlers()
        );
    }

    /**
     * assure that isResponsible() works correct
     *
     * @test
     */
    public function isResponsible()
    {
        $this->mockErrorHandler1->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->onConsecutiveCalls(true, false));
        $this->mockErrorHandler3->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->assertTrue($this->compositeErrorHandler->isResponsible(1, 'foo'));
        $this->assertFalse($this->compositeErrorHandler->isResponsible(1, 'foo'));
    }

    /**
     * assure that isSupressable() works correct
     *
     * @test
     */
    public function isSupressable()
    {
        $this->mockErrorHandler1->expects($this->exactly(2))
                                ->method('isSupressable')
                                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('isSupressable')
                                ->will($this->onConsecutiveCalls(false, true));
        $this->mockErrorHandler3->expects($this->once())
                                ->method('isSupressable')
                                ->will($this->returnValue(true));
        $this->assertFalse($this->compositeErrorHandler->isSupressable(1, 'foo'));
        $this->assertTrue($this->compositeErrorHandler->isSupressable(1, 'foo'));
    }

    /**
     * assure that handle() works correct
     *
     * @test
     */
    public function handleWithoutResonsibility()
    {
        $this->mockErrorHandler1->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler1->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler1->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler2->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler2->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler3->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('handle');
        $this->assertTrue($this->compositeErrorHandler->handle(1, 'foo'));
    }

    /**
     * assure that handle() works correct
     *
     * @test
     */
    public function handleWithSupressAndErrorReportingDisabled()
    {
        $oldLevel = error_reporting(0);
        $this->mockErrorHandler1->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler1->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler1->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler2->expects($this->once())
                                ->method('isResponsible')
                                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->once())
                                ->method('isSupressable')
                                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isResponsible');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('handle');
        $this->assertTrue($this->compositeErrorHandler->handle(1, 'foo'));
        error_reporting($oldLevel);
    }

    /**
     * assure that handle() works correct
     *
     * @test
     */
    public function handleWithoutSupressAndErrorReportingDisabled()
    {
        $oldLevel = error_reporting(0);
        $this->mockErrorHandler1->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler1->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler1->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('isSupressable')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('handle')
                                ->will($this->onConsecutiveCalls(true, false));
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isResponsible');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('handle');
        $this->assertTrue($this->compositeErrorHandler->handle(1, 'foo'));
        $this->assertFalse($this->compositeErrorHandler->handle(1, 'foo'));
        error_reporting($oldLevel);
    }

    /**
     * assure that handle() works correct
     *
     * @test
     */
    public function handleWithErrorReportingEnabled()
    {
        $oldLevel = error_reporting(E_ALL);
        $this->mockErrorHandler1->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler1->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler1->expects($this->never())
                                ->method('handle');
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('isResponsible')
                                ->will($this->returnValue(true));
        $this->mockErrorHandler2->expects($this->any())
                                ->method('isSupressable')
                                ->will($this->returnValue(false));
        $this->mockErrorHandler2->expects($this->exactly(2))
                                ->method('handle')
                                ->will($this->onConsecutiveCalls(true, false));
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isResponsible');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('isSupressable');
        $this->mockErrorHandler3->expects($this->never())
                                ->method('handle');
        $this->assertTrue($this->compositeErrorHandler->handle(1, 'foo'));
        $this->assertFalse($this->compositeErrorHandler->handle(1, 'foo'));
        error_reporting($oldLevel);
    }
}
?>