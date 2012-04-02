<?php
/**
 * Binding module to configure the binder with a runtime mode.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 * @version     $Id: stubModeBindingModule.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder',
                      'net::stubbles::ioc::module::stubBindingModule',
                      'net::stubbles::lang::stubMode'
);
/**
 * Binding module to configure the binder with a runtime mode.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 */
class stubModeBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * mode instance to bind
     *
     * @var  stubMode
     */
    protected $mode;

    /**
     * constructor
     *
     * @param  stubMode  $mode         optional
     * @param  string    $projectPath  optional
     */
    public function __construct(stubMode $mode = null, $projectPath = null)
    {
        if (null === $mode) {
            $mode = $this->getFallbackMode();
        }

        if (null === $projectPath) {
            $projectPath = stubBootstrap::getCurrentProjectPath();
        }
        
        $mode->registerErrorHandler($projectPath);
        $mode->registerExceptionHandler($projectPath);
        $this->mode = $mode;
    }

    /**
     * returns fallback mode
     *
     * @return  stubMode
     */
    protected function getFallbackMode()
    {
        stubClassLoader::load('net::stubbles::lang::stubDefaultMode');
        return stubDefaultMode::prod();
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('stubMode')
               ->toInstance($this->mode);
    }
}
?>