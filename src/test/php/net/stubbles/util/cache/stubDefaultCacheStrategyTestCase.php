<?php
/**
 * Tests for net::stubbles::util::cache::stubDefaultCacheStrategy.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @version     $Id: stubDefaultCacheStrategyTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::stubDefaultCacheStrategy');
/**
 * Tests for net::stubbles::util::cache::stubDefaultCacheStrategy.
 *
 * @package     stubbles
 * @subpackage  util_cache_test
 * @group       util_cache
 */
class stubDefaultCacheStrategyTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDefaultCacheStrategy
     */
    protected $defaultCacheStrategy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultCacheStrategy = new stubDefaultCacheStrategy();
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetTimeToLiveMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setTimeToLiveMethod = $class->getMethod('setTimeToLive');
        $this->assertTrue($setTimeToLiveMethod->hasAnnotation('Inject'));
        $this->assertTrue($setTimeToLiveMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setTimeToLiveMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.timeToLive',
                            $setTimeToLiveMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetMaxCacheSizeMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setMaxCacheSizeMethod = $class->getMethod('setMaxCacheSize');
        $this->assertTrue($setMaxCacheSizeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setMaxCacheSizeMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setMaxCacheSizeMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.maxSize',
                            $setMaxCacheSizeMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetGcProbabilityMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setGcProbabilityMethod = $class->getMethod('setGcProbability');
        $this->assertTrue($setGcProbabilityMethod->hasAnnotation('Inject'));
        $this->assertTrue($setGcProbabilityMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setGcProbabilityMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.gcProbability',
                            $setGcProbabilityMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function isDefaultImplementation()
    {
        $refClass = new stubReflectionClass('net::stubbles::util::cache::stubCacheStrategy');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals($this->defaultCacheStrategy->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function isCachable()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('stubCacheContainer');
        $mockContainer->expects($this->exactly(6))
                      ->method('getUsedSpace')
                      ->will($this->onConsecutiveCalls(1, 1, 1, 2, 2, 0));
        $mockContainer->expects($this->exactly(6))
                      ->method('getSize')
                      ->will($this->onConsecutiveCalls(0, 0, 1, 2, 0, 0));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'a'));
        $this->assertFalse($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'a'));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'a'));
        $this->assertFalse($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'a'));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
        
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(-1)
                                   ->setGcProbability(0);
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isExpired()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('stubCacheContainer');
        $mockContainer->expects($this->exactly(3))
                      ->method('getLifeTime')
                      ->will($this->onConsecutiveCalls(9, 10, 11));
        
        $this->assertFalse($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
        $this->assertFalse($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
        $this->assertTrue($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
    }

    /**
     * @test
     */
    public function shouldRunGc()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('stubCacheContainer');
        $this->assertFalse($this->defaultCacheStrategy->shouldRunGc($mockContainer));
        $this->assertFalse($this->defaultCacheStrategy->shouldRunGc($mockContainer));
        
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(100);
        $this->assertTrue($this->defaultCacheStrategy->shouldRunGc($mockContainer));
        $this->assertTrue($this->defaultCacheStrategy->shouldRunGc($mockContainer));
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @since              1.1.0
     */
    public function setTimeToLiveWithNegativeValueThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setTimeToLive(-1);
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @since              1.1.0
     */
    public function setGcProbabilityWithValueLowerThan0ThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setGcProbability(-1);
    }

    /**
     * @test
     * @expectedException  stubIllegalArgumentException
     * @since              1.1.0
     */
    public function setGcProbabilityWithValueGreaterThan100ThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setGcProbability(101);
    }
}
?>