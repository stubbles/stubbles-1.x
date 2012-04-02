<?php
/**
 * Processor for rss feeds.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 * @version     $Id: stubRSSProcessor.php 3206 2011-11-02 16:57:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::lang::stubProperties',
                      'net::stubbles::lang::exceptions::stubConfigurationException',
                      'net::stubbles::webapp::processor::stubAbstractProcessor',
                      'net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::rss::stubRSSFeed'
);
/**
 * Processor for rss feeds.
 *
 * @package     stubbles
 * @subpackage  xml_rss
 */
class stubRSSProcessor extends stubAbstractProcessor
{
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * list of available rss feeds
     *
     * @var  array<string,string>
     */
    protected $feeds;
    /**
     * name of the route
     *
     * @var  string
     */
    protected $routeName;
    /**
     * rss feed to create
     *
     * @var  stubRSSFeed
     */
    protected $rssFeed;

    /**
     * constructor
     *
     * @param   stubRequest           $request     current request
     * @param   stubSession           $session     current session
     * @param   stubResponse          $response    current response
     * @param   stubInjector          $injector    injector to create feed instances with
     * @param   array<string,string>  $feeds       list of available rss feeds
     * @throws  stubConfigurationException
     * @Inject
     * @Named{feeds}('net.stubbles.xml.rss.feeds')
     */
    public function __construct(stubRequest $request, stubSession $session, stubResponse $response, stubInjector $injector, array $feeds)
    {
        if (count($feeds) === 0) {
            throw new stubConfigurationException('No rss feeds configured');
        }
        
        parent::__construct($request, $session, $response);
        $this->injector = $injector;
        $this->feeds    = $feeds;
    }

    /**
     * detect route and prepare skin
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     * @throws  stubProcessorException
     */
    public function startup(stubUriRequest $uriRequest)
    {
        $this->routeName = $uriRequest->getRemainingUri();
        if (isset($this->feeds[$this->routeName]) === false) {
            throw new stubProcessorException(404, 'Not Found');
        }

        $this->rssFeed = $this->injector->getInstance($this->feeds[$this->routeName]);
        return $this;
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return $this->rssFeed->isCachable();
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return $this->rssFeed->getCacheVars();
    }

    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * processes the request
     *
     * @return  stubProcessor
     */
    public function process()
    {
        $this->response->addHeader('Content-Type', 'text/xml; charset=utf-8');
        $this->response->write($this->rssFeed->create()
                                             ->serialize($this->injector->getInstance('stubXMLStreamWriter'))
                                             ->asXML()
        );
        return $this;
    }
}
?>