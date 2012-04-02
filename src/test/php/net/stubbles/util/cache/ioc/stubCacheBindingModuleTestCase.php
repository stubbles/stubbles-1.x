<?php
/**
 * Test for net::stubbles::util::cache::ioc::stubCacheBindingModule.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 * @version     $Id: stubCacheBindingModuleTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::util::cache::ioc::stubCacheBindingModule');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 */
class DifferentCacheContainerProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * cache container to return
     *
     * @var  stubCacheContainer
     */
    public static $cacheContainer;
    /**
     * returns the requested cache container
     *
     * @param   string                $name  optional  name of requested cache container
     * @return  stubCacheContainer
     */
    public function get($name = null)
    {
        return self::$cacheContainer;
    }
}
/**
 * Helper class to access protected properties.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 */
class DefaultCacheStrategyPropertyAccessor extends stubDefaultCacheStrategy
{
    /**
     * returns list of protected properties and their values
     *
     * @param   stubDefaultCacheStrategy  $defaultCacheStrategy
     * @return  array<string,double>
     */
    public static function getProperties(stubDefaultCacheStrategy $defaultCacheStrategy)
    {
        return array('ttl'           => $defaultCacheStrategy->timeToLive,
                     'maxSize'       => $defaultCacheStrategy->maxSize,
                     'gcProbability' => $defaultCacheStrategy->gcProbability
        
               );
    }
}
/**
 * Test for net::stubbles::util::cache::ioc::stubCacheBindingModule.
 *
 * @package     stubbles
 * @subpackage  util_cache_ioc_test
 * @group       util_cache
 * @group       util_cache_ioc
 */
class stubCacheBindingModuleTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCacheBindingModule
     */
    protected $cacheBindingModule;
    /**
     * binder instance to be used
     *
     * @var  stubBinder
     */
    protected $binder;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->cacheBindingModule = new stubCacheBindingModule(dirname(__FILE__));
        $this->binder             = new stubBinder();
    }

    /**
     * setting file mode returns the instance
     *
     * @test
     */
    public function defaultBindings()
    {
        $this->cacheBindingModule = new stubCacheBindingModule();
        $this->cacheBindingModule->configure($this->binder);
        $injector = $this->binder->getInjector();
        $this->assertFalse($injector->hasBinding(stubConstantBinding::TYPE, 'net.stubbles.cache.path'));
        $this->assertTrue($injector->hasBinding('stubCacheStrategy'));
        $this->assertTrue($injector->hasBinding('stubCacheContainer'));
        $cacheStrategy = $injector->getInstance('stubCacheStrategy');
        $this->assertInstanceOf('stubDefaultCacheStrategy', $cacheStrategy);
        $this->assertEquals(array('ttl'           => 86400,
                                  'maxSize'       => -1,
                                  'gcProbability' => 10
                    
                            ),
                            DefaultCacheStrategyPropertyAccessor::getProperties($cacheStrategy)
        
        );
    }

    /**
     * setting file mode returns the instance
     *
     * @test
     */
    public function defaultBindingsWithCachePath()
    {
        $this->cacheBindingModule->configure($this->binder);
        $injector = $this->binder->getInjector();
        $this->assertTrue($injector->hasBinding(stubConstantBinding::TYPE, 'net.stubbles.cache.path'));
        $this->assertTrue($injector->hasBinding('stubCacheStrategy'));
        $this->assertTrue($injector->hasBinding('stubCacheContainer'));
        $cacheStrategy = $injector->getInstance('stubCacheStrategy');
        $this->assertInstanceOf('stubDefaultCacheStrategy', $cacheStrategy);
        $this->assertEquals(array('ttl'           => 86400,
                                  'maxSize'       => -1,
                                  'gcProbability' => 10
                    
                            ),
                            DefaultCacheStrategyPropertyAccessor::getProperties($cacheStrategy)
        
        );
    }

    /**
     * different cache strategy properties should be set
     *
     * @test
     */
    public function differentCacheStrategyProperties()
    {
        $this->assertSame($this->cacheBindingModule, $this->cacheBindingModule->setDefaultStrategyValues(100, 100, 0));
        $this->cacheBindingModule->configure($this->binder);
        $injector = $this->binder->getInjector();
        $this->assertTrue($injector->hasBinding('stubCacheStrategy'));
        $cacheStrategy = $injector->getInstance('stubCacheStrategy');
        $this->assertInstanceOf('stubDefaultCacheStrategy', $cacheStrategy);
        $this->assertEquals(array('ttl'           => 100,
                                  'maxSize'       => 100,
                                  'gcProbability' => 0
                    
                            ),
                            DefaultCacheStrategyPropertyAccessor::getProperties($cacheStrategy)
        
        );
        $this->assertInstanceOf('stubFileCacheContainer', $injector->getInstance('stubCacheContainer'));
    }

    /**
     * different cache container provider class should be used
     *
     * @test
     */
    public function differentCacheContainerProvider()
    {
        DifferentCacheContainerProvider::$cacheContainer = $this->getMock('stubCacheContainer');
        $cacheBindingModule = stubCacheBindingModule::create(dirname(__FILE__),
                                                             'DifferentCacheContainerProvider'
                              );
        $cacheBindingModule->configure($this->binder);
        $injector = $this->binder->getInjector();
        $this->assertTrue($injector->hasBinding(stubConstantBinding::TYPE, 'net.stubbles.cache.path'));
        $this->assertEquals(dirname(__FILE__), $injector->getInstance(stubConstantBinding::TYPE, 'net.stubbles.cache.path'));
        $this->assertSame(DifferentCacheContainerProvider::$cacheContainer, $injector->getInstance('stubCacheContainer'));
    }

    /**
     * different cache strategy should be used
     *
     * @test
     */
    public function differentCacheStrategy()
    {
        $mockCacheStrategy = $this->getMock('stubCacheStrategy');
        $this->assertSame($this->cacheBindingModule, $this->cacheBindingModule->setCacheStrategy($mockCacheStrategy));
        $this->cacheBindingModule->configure($this->binder);
        $injector = $this->binder->getInjector();
        $this->assertTrue($injector->hasBinding('stubCacheStrategy'));
        $this->assertSame($mockCacheStrategy, $injector->getInstance('stubCacheStrategy'));
    }
}
?>