<?php
/**
 * Test for net::stubbles::webapp::xml::skin::stubSkinGeneratorProvider.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin_test
 * @version     $Id: stubSkinGeneratorProviderTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubDefaultMode',
                      'net::stubbles::webapp::xml::skin::stubSkinGeneratorProvider'
);
/**
 * Test for net::stubbles::webapp::xml::skin::stubSkinGeneratorProvider.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_skin
 */
class stubSkinGeneratorProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * injector instance
     *
     * @var  stubBinder
     */
    protected $binder;
    /**
     * mocked default skin generator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockDefaultSkinGenerator;
    /**
     * mocked caching skin generator
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCachingSkinGenerator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockDefaultSkinGenerator = $this->getMock('stubSkinGenerator');
        $this->mockCachingSkinGenerator = $this->getMock('stubSkinGenerator');
        $binder                         = new stubBinder();
        $binder->bind('stubSkinGenerator')
               ->named('webapp.xml.skin.default')
               ->toInstance($this->mockDefaultSkinGenerator);
        $binder->bind('stubSkinGenerator')
               ->named('webapp.xml.skin.cached')
               ->toInstance($this->mockCachingSkinGenerator);
        $this->injector = $binder->getInjector();
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $refConstructor = new stubReflectionMethod('stubSkinGeneratorProvider', '__construct');
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));
        
        $setModeMethod = new stubReflectionMethod('stubSkinGeneratorProvider', 'setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * no mode should deliver caching skin generator
     *
     * @test
     */
    public function noModeDeliversCachingSkinGenerator()
    {
        $skinGeneratorProvider = new stubSkinGeneratorProvider($this->injector);
        $this->assertSame($this->mockCachingSkinGenerator, $skinGeneratorProvider->get());
    }

    /**
     * prod mode should deliver caching skin generator
     *
     * @test
     */
    public function prodModeDeliversCachingSkinGenerator()
    {
        $skinGeneratorProvider = new stubSkinGeneratorProvider($this->injector);
        $skinGeneratorProvider->setMode(stubDefaultMode::prod());
        $this->assertSame($this->mockCachingSkinGenerator, $skinGeneratorProvider->get());
    }

    /**
     * test mode should deliver caching skin generator
     *
     * @test
     */
    public function testModeDeliversCachingSkinGenerator()
    {
        $skinGeneratorProvider = new stubSkinGeneratorProvider($this->injector);
        $skinGeneratorProvider->setMode(stubDefaultMode::test());
        $this->assertSame($this->mockCachingSkinGenerator, $skinGeneratorProvider->get());
    }

    /**
     * test mode should deliver caching skin generator
     *
     * @test
     */
    public function stageModeDeliversDefaultSkinGenerator()
    {
        $skinGeneratorProvider = new stubSkinGeneratorProvider($this->injector);
        $skinGeneratorProvider->setMode(stubDefaultMode::stage());
        $this->assertSame($this->mockDefaultSkinGenerator, $skinGeneratorProvider->get());
    }

    /**
     * test mode should deliver caching skin generator
     *
     * @test
     */
    public function devModeDeliversDefaultSkinGenerator()
    {
        $skinGeneratorProvider = new stubSkinGeneratorProvider($this->injector);
        $skinGeneratorProvider->setMode(stubDefaultMode::dev());
        $this->assertSame($this->mockDefaultSkinGenerator, $skinGeneratorProvider->get());
    }

}
?>