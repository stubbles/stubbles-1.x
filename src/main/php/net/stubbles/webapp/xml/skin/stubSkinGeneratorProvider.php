<?php
/**
 * Provides skin generator instances depending on the current runtime mode.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 * @version     $Id: stubSkinGeneratorProvider.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::lang::stubMode'
);
/**
 * Provides skin generator instances depending on the current runtime mode.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin
 */
class stubSkinGeneratorProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * current mode we are running in
     *
     * @var  stubMode
     */
    protected $mode;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * sets the runtime mode we are running in
     *
     * @param  stubMode      $mode
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->mode = $mode;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        if (null === $this->mode || $this->mode->isCacheEnabled() === true) {
            return $this->injector->getInstance('stubSkinGenerator', 'webapp.xml.skin.cached');
        }
        
        return $this->injector->getInstance('stubSkinGenerator', 'webapp.xml.skin.default');
    }
}
?>