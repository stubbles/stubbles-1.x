<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubStrategyFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubStrategyFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubStrategyFilterDecorator');
abstract class TeststubStrategyFilterDecorator extends stubStrategyFilterDecorator
{
    /**
     * sets the strategy to be applied
     *
     * @param   int  $strategy
     * @throws  stubIllegalArgumentException
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::stubStrategyFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubStrategyFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the minimum validator
     *
     * @var  stubStrategyFilterDecorator
     */
    protected $strategyFilterDecorator;
    /**
     * mocked decorated filter instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockFilter;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->strategyFilterDecorator = $this->getMock('stubStrategyFilterDecorator',
                                                        array('getDecoratedFilter',
                                                              'doExecute'
                                                        )
                                         );
        $this->mockFilter              = $this->getMock('stubFilter');
        $this->strategyFilterDecorator->expects($this->any())
                                      ->method('getDecoratedFilter')
                                      ->will($this->returnValue($this->mockFilter));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function beforeStrategy()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_BEFORE);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo('foo'))
                                      ->will($this->returnValue('bar'));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo('bar'))
                         ->will($this->returnValue('baz'));
        $this->assertEquals('baz', $this->strategyFilterDecorator->execute('foo'));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function beforeStrategyEmpty()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_BEFORE);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo(''))
                                      ->will($this->returnValue(''));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo(''))
                         ->will($this->returnValue(''));
        $this->assertEquals('', $this->strategyFilterDecorator->execute(''));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function beforeStrategyNull()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_BEFORE);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo(null))
                                      ->will($this->returnValue(null));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo(null))
                         ->will($this->returnValue(null));
        $this->assertNull($this->strategyFilterDecorator->execute(null));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function afterStrategy()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_AFTER);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo('bar'))
                                      ->will($this->returnValue('baz'));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo('foo'))
                         ->will($this->returnValue('bar'));
        $this->assertEquals('baz', $this->strategyFilterDecorator->execute('foo'));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function afterStrategyEmpty()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_AFTER);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo(''))
                                      ->will($this->returnValue(''));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo(''))
                         ->will($this->returnValue(''));
        $this->assertEquals('', $this->strategyFilterDecorator->execute(''));
    }

    /**
     * test that encoder is applied correct
     *
     * @test
     */
    public function afterStrategyNull()
    {
        $this->strategyFilterDecorator->setStrategy(stubStrategyFilterDecorator::STRATEGY_AFTER);
        $this->strategyFilterDecorator->expects($this->once())
                                      ->method('doExecute')
                                      ->with($this->equalTo(null))
                                      ->will($this->returnValue(null));
        $this->mockFilter->expects($this->once())
                         ->method('execute')
                         ->with($this->equalTo(null))
                         ->will($this->returnValue(null));
        $this->assertNull($this->strategyFilterDecorator->execute(null));
    }

    /**
     * try to set an illegal strategy value
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function illegalStrategy()
    {
        $this->strategyFilterDecorator->setStrategy(0);
    }

    /**
     * make use of an illegal strategy at runtime
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function illegalStrategyAtRuntime()
    {
        $strategyFilterDecorator = $this->getMock('TeststubStrategyFilterDecorator',
                                                  array('getDecoratedFilter',
                                                        'doExecute'
                                                  )
                                   );
        $strategyFilterDecorator->setStrategy(0);
        $strategyFilterDecorator->expects($this->never())->method('getDecoratedFilter');
        $strategyFilterDecorator->expects($this->never())->method('doExecute');
        $strategyFilterDecorator->execute('foo');
    }
}
?>