<?php
/**
 * Abstract base class for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @version     $Id: stubWebApp.php 3270 2011-12-05 18:23:21Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubModeBindingModule',
                      'net::stubbles::ioc::module::stubPropertiesBindingModule',
                      'net::stubbles::ipo::ioc::stubIpoBindingModule',
                      'net::stubbles::webapp::stubUriConfigurator',
                      'net::stubbles::webapp::stubWebAppFrontController',
                      'net::stubbles::webapp::ioc::stubWebAppBindingModule'
);
/**
 * Abstract base class for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp
 * @since       1.7.0
 */
class stubWebApp extends stubBaseObject
{
    /**
     * front controller
     *
     * @var  stubWebAppFrontController
     */
    protected $webAppFrontController;

    /**
     * constructor
     *
     * @param  stubWebAppFrontController  $webAppFrontController
     * @Inject
     */
    public function  __construct(stubWebAppFrontController $webAppFrontController)
    {
        $this->webAppFrontController = $webAppFrontController;
    }

    /**
     * runs the application
     */
    public function run()
    {
        $this->webAppFrontController->process();
    }

    /**
     * creates mode binding module
     *
     * @param   stubMode               $mode  optional
     * @return  stubModeBindingModule
     */
    protected static function createModeBindingModule(stubMode $mode = null)
    {
        return new stubModeBindingModule($mode);
    }

    /**
     * creates properties binding module
     *
     * @param   string                       $projectPath
     * @return  stubPropertiesBindingModule
     */
    protected static function createPropertiesBindingModule($projectPath)
    {
        return new stubPropertiesBindingModule($projectPath);
    }

    /**
     * creates ipo binding module
     *
     * @param   string                $sessionName
     * @return  stubIpoBindingModule
     */
    protected static function createIpoBindingModule($sessionName = 'PHPSESSID')
    {
        return stubIpoBindingModule::create($sessionName);
    }

    /**
     * creates log binding module
     *
     * @return  stubLogBindingModule
     */
    protected static function createLogBindingModule()
    {
        stubClassLoader::load('net::stubbles::util::log::ioc::stubLogBindingModule');
        return stubLogBindingModule::create();
    }

    /**
     * creates web app binding module
     *
     * @param   stubUriConfigurator      $uriConfigurator
     * @return  stubWebAppBindingModule
     */
    protected static function createWebAppBindingModule(stubUriConfigurator $uriConfigurator)
    {
        return stubWebAppBindingModule::create($uriConfigurator);
    }

    /**
     * creates uri configurator with xml processor as default
     *
     * @param   string               $defaultName   name of fallback processor
     * @param   string               $defaultClass  class name of fallback processor
     * @return  stubUriConfigurator
     */
    protected static function createUriConfigurator($defaultName, $defaultClass = null)
    {
        return stubUriConfigurator::create($defaultName, $defaultClass);
    }

    /**
     * creates uri configurator with xml processor as default
     *
     * @return  stubUriConfigurator
     */
    protected static function createXmlUriConfigurator()
    {
        return stubUriConfigurator::createWithXmlProcessorAsDefault();
    }

    /**
     * creates uri configurator with xml processor as default
     *
     * @return  stubUriConfigurator
     */
    protected static function createRestUriConfigurator()
    {
        return stubUriConfigurator::createWithRestProcessorAsDefault();
    }
}
?>