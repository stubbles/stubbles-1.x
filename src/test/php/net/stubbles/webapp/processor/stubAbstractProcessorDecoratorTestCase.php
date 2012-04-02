<?php
/**
 * Tests for net::stubbles::webapp::processor::stubAbstractProcessorDecorator.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 * @version     $Id: stubAbstractProcessorDecoratorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::processor::stubAbstractProcessorDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 */
class TeststubAbstractProcessorDecorator extends stubAbstractProcessorDecorator
{
    /**
     * constructor
     *
     * @param  stubProcessor  $processor
     */
    public function __construct(stubProcessor $processor)
    {
        $this->processor = $processor;
    }
}
/**
 * Tests for net::stubbles::webapp::processor::stubAbstractProcessorDecorator.
 *
 * @package     stubbles
 * @subpackage  webapp_processor_test
 * @group       webapp
 * @group       webapp_processor
 */
class stubAbstractProcessorDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to be used for tests
     *
     * @var  TeststubAbstractProcessorDecorator
     */
    protected $abstractProcessorDecorator;
    /**
     * mocked decorated processor
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockProcessor;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockProcessor              = $this->getMock('stubProcessor');
        $this->abstractProcessorDecorator = new TeststubAbstractProcessorDecorator($this->mockProcessor);
    }

    /**
     * startup() is called
     *
     * @test
     */
    public function startupIsCalled()
    {
        $uriRequest = new stubUriRequest('/');
        $this->mockProcessor->expects($this->once())
                            ->method('startup')
                            ->with($this->equalTo($uriRequest));
        $this->assertSame($this->abstractProcessorDecorator,
                          $this->abstractProcessorDecorator->startup($uriRequest)
        );
    }

    /**
     * getRequiredRole() is called
     *
     * @test
     */
    public function getRequiredRoleIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRequiredRole')
                            ->with($this->equalTo('defaultRole'))
                            ->will($this->returnValue('otherRole'));
        $this->assertEquals('otherRole', $this->abstractProcessorDecorator->getRequiredRole('defaultRole'));
    }

    /**
     * isCachable() is called
     *
     * @test
     */
    public function isCachableIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('isCachable')
                            ->will($this->returnValue(false));
        $this->assertFalse($this->abstractProcessorDecorator->isCachable());
    }

    /**
     * getCacheVars() is called
     *
     * @test
     */
    public function getCacheVarsIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getCacheVars')
                            ->will($this->returnValue(array('var1' => 'value1')));
        $this->assertEquals(array('var1' => 'value1'), $this->abstractProcessorDecorator->getCacheVars());
    }

    /**
     * getRouteName() is called
     *
     * @test
     */
    public function getRouteNameIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('getRouteName')
                            ->will($this->returnValue('index'));
        $this->assertEquals('index', $this->abstractProcessorDecorator->getRouteName());
    }

    /**
     * forceSsl() is called
     *
     * @test
     */
    public function forceSslIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('forceSsl')
                            ->will($this->returnValue(false));
        $this->assertFalse($this->abstractProcessorDecorator->forceSsl());
    }

    /**
     * isSsl() is called
     *
     * @test
     */
    public function isSslIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('isSsl')
                            ->will($this->returnValue(false));
        $this->assertFalse($this->abstractProcessorDecorator->isSsl());
    }

    /**
     * process() is called
     *
     * @test
     */
    public function processIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('process');
        $this->assertSame($this->abstractProcessorDecorator,
                          $this->abstractProcessorDecorator->process()
        );
    }

    /**
     * cleanup() is called
     *
     * @test
     */
    public function cleanupIsCalled()
    {
        $this->mockProcessor->expects($this->once())
                            ->method('cleanup');
        $this->assertSame($this->abstractProcessorDecorator,
                          $this->abstractProcessorDecorator->cleanup()
        );
    }

}
?>