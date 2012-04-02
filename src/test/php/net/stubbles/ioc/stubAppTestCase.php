<?php
/**
 * Test for net::stubbles::ioc::stubApp.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubAppTestCase.php 3220 2011-11-14 15:33:46Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleCommand',
                      'net::stubbles::ioc::stubApp',
                      'net::stubbles::ioc::module::stubPropertiesBindingModule',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::util::cache::ioc::stubCacheBindingModule',
                      'net::stubbles::webapp::stubUriConfigurator',
                      'net::stubbles::webapp::ioc::stubWebAppBindingModule',
                      'net::stubbles::websites::ioc::stubWebsiteBindingModule'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubAppTestBindingModuleOne extends stubBaseObject implements stubBindingModule
{
    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('foo')->to('stdClass');
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubAppTestBindingModuleTwo extends stubBaseObject implements stubBindingModule
{
    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('bar')->to('stdClass');
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubAppCommandWithBindings extends stubBaseObject implements stubConsoleCommand
{
    /**
     * given project path
     *
     * @var  string
     */
    protected static $projectPath;

    /**
     * return list of bindings required for this command
     *
     * @param   string                           $projectPath
     * @return  array<string|stubBindingModule>
     */
    public static function __bindings($projectPath)
    {
        self::$projectPath = $projectPath;
        return array(new stubAppTestBindingModuleOne(),
                     new stubAppTestBindingModuleTwo()
               );
    }

    /**
     * returns set project path
     *
     * @return  string
     */
    public static function getProjectPath()
    {
        return self::$projectPath;
    }

    /**
     * runs the command
     */
    public function run() { }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubAppClassWithArgument extends stubBaseObject
{
    /**
     * given project path
     *
     * @var  string
     */
    protected $arg;

    /**
     * returns set project path
     *
     * @return  string
     * @Inject
     * @Named('argv.0')
     */
    public function setArgument($arg)
    {
        $this->arg = $arg;
    }

    /**
     * returns the argument
     *
     * @return  string
     */
    public function getArgument()
    {
        return $this->arg;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubAppTestIpoBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * request instance
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * session instance
     *
     * @var  stubSession
     */
    protected $session;
    /**
     * response instance
     *
     * @var  stubResponse
     */
    protected $response;
    
    /**
     * constructor
     *
     * @param  stubRequest   $request
     * @param  stubSession   $session
     * @param  stubResponse  $response
     */
    public function __construct(stubRequest $request, stubSession $session, stubResponse $response)
    {
        $this->request  = $request;
        $this->session  = $session;
        $this->response = $response;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('stubRequest')->toInstance($this->request);
        $binder->bind('stubSession')->toInstance($this->session);
        $binder->bind('stubResponse')->toInstance($this->response);
    }
}
/**
 * Test for net::stubbles::ioc::stubApp.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubAppTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * invalid binding module class throws illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidBindingModuleClassThrowsIllegalArgumentException()
    {
        stubApp::createInjector('stdClass');
    }

    /**
     * invalid binding module instance throws illegal argument exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException()
    {
        stubApp::createInjector(new stdClass());
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = stubApp::createInjector(new stubAppTestBindingModuleOne(),
                                            'stubAppTestBindingModuleTwo'
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('stubInjector'));
        $this->assertSame($injector, $injector->getInstance('stubInjector'));
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function bindingModulesAreProcessedIfPassedAsArray()
    {
        $injector = stubApp::createInjector(array(new stubAppTestBindingModuleOne(),
                                                  'stubAppTestBindingModuleTwo'
                                            )
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('stubInjector'));
        $this->assertSame($injector, $injector->getInstance('stubInjector'));
    }

    /**
     * @test
     * @deprecated
     */
    public function frontControllerCanBeCreatedWithDefaultBindings()
    {
        $frontController = stubApp::createFrontController(new stubAppTestIpoBindingModule($this->getMock('stubRequest'),
                                                                                          $this->getMock('stubSession'),
                                                                                          $this->getMock('stubResponse')
                                                          ),
                                                          new stubPropertiesBindingModule(stubPathRegistry::getConfigPath() . '/../'),
                                                          new stubCacheBindingModule(stubPathRegistry::getCachePath()),
                                                          stubWebsiteBindingModule::createWithXmlProcessorAsDefault()
                           );
        $this->assertInstanceOf('stubFrontController', $frontController);
    }

    /**
     * @test
     * @since  1.6.0
     * @deprecated
     */
    public function frontControllerCanBeCreatedWithDefaultBindingsAsArray()
    {
        $frontController = stubApp::createFrontController(array(new stubAppTestIpoBindingModule($this->getMock('stubRequest'),
                                                                                                $this->getMock('stubSession'),
                                                                                                $this->getMock('stubResponse')
                                                                ),
                                                                new stubPropertiesBindingModule(stubPathRegistry::getConfigPath() . '/../'),
                                                                new stubCacheBindingModule(stubPathRegistry::getCachePath()),
                                                                stubWebsiteBindingModule::createWithXmlProcessorAsDefault()
                                                          )
                           );
        $this->assertInstanceOf('stubFrontController', $frontController);
    }

    /**
     * createInstance() creates an instance using bindings
     *
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = stubApp::createInstance('stubAppCommandWithBindings', 'projectPath');
        $this->assertInstanceOf('stubAppCommandWithBindings', $appCommandWithBindings);
        $this->assertEquals('projectPath', stubAppCommandWithBindings::getProjectPath());
    }

    /**
     * createInstance() creates an instance without bindings
     *
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('stubAppTestBindingModuleTwo',
                          stubApp::createInstance('stubAppTestBindingModuleTwo', 'projectPath')
        );
    }

    /**
     * createInstance() creates an instance with arguments
     *
     * @test
     */
    public function createInstanceWithArguments()
    {
        $appClassWithArgument = stubApp::createInstance('stubAppClassWithArgument', 'projectPath', array('foo'));
        $this->assertInstanceOf('stubAppClassWithArgument', $appClassWithArgument);
        $this->assertEquals('foo', $appClassWithArgument->getArgument());
    }
}
?>