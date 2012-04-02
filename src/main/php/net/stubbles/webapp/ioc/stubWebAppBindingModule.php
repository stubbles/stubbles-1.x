<?php
/**
 * Binding module for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp_ioc
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule',
                      'net::stubbles::webapp::stubUriConfigurator'
);
/**
 * Binding module for web applications.
 *
 * @package     stubbles
 * @subpackage  webapp_ioc
 * @since       1.7.0
 */
class stubWebAppBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * url configuration
     *
     * @var  stubUriConfigurator
     */
    protected $uriConfigurator;
    /**
     * switch whether auth processor is enabled or not
     *
     * @var  bool
     */
    protected $authEnabled      = false;
    /**
     * list of default xml generators
     *
     * @var  array<string>
     */
    protected $xmlGenerators    = array('net::stubbles::webapp::xml::generator::stubSessionXmlGenerator',
                                        'net::stubbles::webapp::xml::generator::stubRouteXmlGenerator',
                                        'net::stubbles::webapp::xml::generator::stubRequestXmlGenerator',
                                        'net::stubbles::webapp::xml::generator::stubModeXmlGenerator',
                                        'net::stubbles::webapp::xml::generator::stubVariantListGenerator'
                                  );
    /**
     * router class to be used by xml processor
     *
     * @var  string
     */
    protected $routeReaderClass = 'net::stubbles::webapp::xml::route::stubPropertyBasedRouteReader';

    /**
     * constructor
     *
     * @param  stubUriConfigurator  $uriConfigurator
     */
    public function __construct(stubUriConfigurator $uriConfigurator)
    {
        $this->uriConfigurator = $uriConfigurator;
    }

    /**
     * static constructor
     *
     * @param   stubUriConfigurator      $uriConfig
     * @return  stubWebAppBindingModule
     */
    public static function create(stubUriConfigurator $uriConfig)
    {
        return new self($uriConfig);
    }

    /**
     * enable auth processor
     *
     * @return  stubWebAppBindingModule
     */
    public function enableAuth()
    {
        $this->authEnabled = true;
        return $this;
    }

    /**
     * add a xml generator for xml processor
     *
     * @param   string                   $xmlGenerator
     * @return  stubWebAppBindingModule
     */
    public function addXmlGenerator($xmlGenerator)
    {
        $this->xmlGenerators[] = $xmlGenerator;
        return $this;
    }

    /**
     * sets the route reader class to be used by xml processor
     *
     * @param   string                   $routeReaderClass
     * @return  stubWebAppBindingModule
     */
    public function setRouteReaderClass($routeReaderClass)
    {
        $this->routeReaderClass = $routeReaderClass;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bindConstant()
               ->named('net.stubbles.webapp.auth')
               ->to($this->authEnabled);
        $binder->bindConstant()
               ->named('net.stubbles.webapp.processor.map')
               ->to($this->uriConfigurator->getProcessorMap());
        $binder->bind('stubProcessor')
               ->toProviderClass('net::stubbles::webapp::ioc::stubProcessorProvider');
        $binder->bind('stubUriConfiguration')
               ->toInstance($this->uriConfigurator->getConfig());
        if ($this->uriConfigurator->isProcessorEnabled('xml') === true) {
            $binder->bind('stubRouteReader')
                   ->to($this->routeReaderClass);
            $binder->bind('stubSkinGenerator')
                   ->named('webapp.xml.skin.default')
                   ->to('net::stubbles::webapp::xml::skin::stubDefaultSkinGenerator');
            $binder->bind('stubSkinGenerator')
                   ->named('webapp.xml.skin.cached')
                   ->to('net::stubbles::webapp::xml::skin::stubCachingSkinGenerator');
            $binder->bind('stubSkinGenerator')
                   ->toProviderClass('net::stubbles::webapp::xml::skin::stubSkinGeneratorProvider');
            $binder->bindConstant()
                   ->named('net.stubbles.webapp.xml.generators')
                   ->to($this->xmlGenerators);
        }

        if ($this->uriConfigurator->isProcessorEnabled('rest') === true) {
            $binder->bindConstant()
                   ->named('net.stubbles.service.rest.handler')
                   ->to($this->uriConfigurator->getRestHandler());
        }

        if ($this->uriConfigurator->isProcessorEnabled('rss') === true) {
            $binder->bindConstant()
                   ->named('net.stubbles.xml.rss.feeds')
                   ->to($this->uriConfigurator->getRssFeeds());
        }
    }
}
?>