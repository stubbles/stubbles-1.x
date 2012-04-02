<?php
/**
 * Test for net::stubbles::webapp::stubWebApp.
 *
 * @package     stubbles
 * @subpackage  webapp_test
 * @version     $Id: stubWebAppTestCase.php 3268 2011-12-05 15:07:35Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::stubWebApp');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_test
 */
class TeststubWebApp extends stubWebApp
{
    /**
     * call method with given name and parameters and return its return value
     *
     * @param   string      $methodName
     * @param   string      $param1      optional
     * @param   string      $param2      optional
     * @return  stubObject
     */
    public static function callMethod($methodName, $param1 = null, $param2 = null)
    {
        return self::$methodName($param1, $param2);
    }
}
/**
 * Test for net::stubbles::webapp::stubWebApp.
 *
 * @package     stubbles
 * @subpackage  webapp_test
 * @since       1.7.0
 * @group       webapp
 */
class stubWebAppTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubWebApp
     */
    protected $webApp;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockWebAppFrontController = $this->getMock('stubWebAppFrontController',
                                                          array(),
                                                          array(),
                                                          '',
                                                          false
                                           );
        $this->webApp = new stubWebApp($this->mockWebAppFrontController);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->webApp->getClass()
                                       ->getConstructor()
                                       ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function runProcessesFrontController()
    {
        
        $this->mockWebAppFrontController->expects($this->once())
                                        ->method('process');
        $this->webApp->run();
    }

    /**
     * @test
     */
    public function canCreateModeBindingModule()
    {
        $this->assertInstanceOf('stubModeBindingModule',
                                TeststubWebApp::callMethod('createModeBindingModule')
        );
    }

    /**
     * @test
     */
    public function canCreatePropertiesBindingModule()
    {
        $this->assertInstanceOf('stubPropertiesBindingModule',
                                TeststubWebApp::callMethod('createPropertiesBindingModule', '/tmp')
        );
    }

    /**
     * @test
     */
    public function canCreateIpoBindingModule()
    {
        $this->assertInstanceOf('stubIpoBindingModule',
                                TeststubWebApp::callMethod('createIpoBindingModule')
        );
    }

    /**
     * @test
     */
    public function canCreateLogBindingModule()
    {
        $this->assertInstanceOf('stubLogBindingModule',
                                TeststubWebApp::callMethod('createLogBindingModule')
        );
    }

    /**
     * @test
     */
    public function canCreateWebAppBindingModule()
    {
        $this->assertInstanceOf('stubWebAppBindingModule',
                                TeststubWebApp::callMethod('createWebAppBindingModule',
                                                           $this->getMock('stubUriConfigurator', array(), array(), '', false)
                                )
        );
    }

    /**
     * @test
     */
    public function canCreateUriConfigurator()
    {
        $this->assertInstanceOf('stubUriConfigurator',
                                TeststubWebApp::callMethod('createUriConfigurator',
                                                           'test',
                                                           'test::TestProcessor'
                                )
        );
    }

    /**
     * @test
     */
    public function canCreateXmlUriConfigurator()
    {
        $this->assertInstanceOf('stubUriConfigurator',
                                TeststubWebApp::callMethod('createXmlUriConfigurator')
        );
    }

    /**
     * @test
     */
    public function canCreateRestUriConfigurator()
    {
        $this->assertInstanceOf('stubUriConfigurator',
                                TeststubWebApp::callMethod('createRestUriConfigurator')
        );
    }
}
?>