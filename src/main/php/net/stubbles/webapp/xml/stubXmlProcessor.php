<?php
/**
 * Default processor delivered by stubbles.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 * @version     $Id: stubXmlProcessor.php 3209 2011-11-10 20:51:33Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::processor::stubAbstractProcessor',
                      'net::stubbles::webapp::processor::stubProcessorException',
                      'net::stubbles::webapp::xml::stubXmlProcessorTransformer',
                      'net::stubbles::webapp::xml::generator::stubXmlGeneratorFacade',
                      'net::stubbles::webapp::xml::route::stubRouteReader',
                      'net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer'
);
/**
 * Default processor delivered by stubbles.
 *
 * @package     stubbles
 * @subpackage  webapp_xml
 */
class stubXmlProcessor extends stubAbstractProcessor
{
    /**
     * route configuration reader
     *
     * @var  stubRouteReader
     */
    protected $routeReader;
    /**
     * route to display
     *
     * @var  stubRoute
     */
    protected $route;
    /**
     * generator facade to hide complexity of managing each single generator instance
     *
     * @var  stubXmlGeneratorFacade
     */
    protected $xmlGeneratorFacade;
    /**
     * transforms generated xml data to target format using xsl
     *
     * @var  stubXmlProcessorTransformer
     */
    protected $xmlTransformer;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param   stubRequest                  $request             current request
     * @param   stubSession                  $session             current session
     * @param   stubResponse                 $response            current response
     * @param   stubRouteReader              $routeReader         read route configuration
     * @param   stubXmlGeneratorFacade       $xmlGeneratorFacade  generator facade to hide single generator instances
     * @param   stubXmlProcessorTransformer  $xmlTransformer      transforms generated xml data to target format using xsl
     * @param   stubInjector                 $injector
     * @Inject
     */
    public function __construct(stubRequest $request,
                                stubSession $session,
                                stubResponse $response,
                                stubRouteReader $routeReader,
                                stubXmlGeneratorFacade $xmlGeneratorFacade,
                                stubXmlProcessorTransformer $xmlTransformer,
                                stubInjector $injector)
    {
        parent::__construct($request, $session, $response);
        $this->xmlGeneratorFacade = $xmlGeneratorFacade;
        $this->xmlTransformer     = $xmlTransformer;
        $this->injector           = $injector;
        $this->routeReader        = $routeReader;
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
        $this->route = $this->routeReader->getRoute($uriRequest->getRemainingUri('index'));
        if (null === $this->route) {
            $this->route = $this->routeReader->getRoute('error404');
            if (null === $this->route) {
                $this->request->cancel();
                throw new stubProcessorException(404, 'Not Found');
            } else {
                $this->response->setStatusCode(404);
            }
        }

        $this->session->putValue('net.stubbles.webapp.lastPage',
                                 $this->route->getProperty('name')
        );
        $this->injector->bind('stubRoute')->toInstance($this->route);
        if ($uriRequest instanceof stubDummyUriRequest) {
            $processorUri = '/xml/';
        } else {
            $processorUri = $uriRequest->getProcessorUri();
        }
        
        $this->xmlTransformer->selectSkin($this->request->readParam('frame')->unsecure(),
                                          $this->route->getProperty('skin')
                               )
                             ->selectLocale($this->session->getValue('net.stubbles.locale'),
                                            $this->route->getProperty('locale')
                               )
                             ->setProcessorUri($processorUri);
        $this->xmlGeneratorFacade->startup();
        return $this;
    }

    /**
     * returns the required role of the user to be able to process the request
     *
     * @param   string  $defaultRole  a default role to return if no special role is required
     * @return  string
     */
    public function getRequiredRole($defaultRole)
    {
        return $this->route->getProperty('role', $defaultRole);
    }

    /**
     * checks whether the current request forces ssl or not
     *
     * @return  bool
     */
    public function forceSsl()
    {
        return $this->route->getPropertyAsBool('forceSsl', false);
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return $this->xmlGeneratorFacade->isCachable();
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return array_merge(array('route'  => $this->route->getProperty('name'),
                                 'skin'   => $this->xmlTransformer->getSelectedSkinName(),
                                 'locale' => $this->xmlTransformer->getSelectedLocale()
                           ),
                           $this->xmlGeneratorFacade->getCacheVars()
               );
    }

    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return $this->route->getProperty('name');
    }

    /**
     * processes the request
     *
     * @return  stubProcessor
     */
    public function process()
    {
        $xmlStreamWriter = $this->injector->getInstance('stubXMLStreamWriter');
        $xmlStreamWriter->writeStartElement('document');
        $xmlStreamWriter->writeAttribute('page', $this->route->getProperty('name'));
        $xmlSerializer = $this->injector->getInstance('stubXMLSerializer');
        $this->xmlGeneratorFacade->generate($xmlStreamWriter, $xmlSerializer);
        if ($this->request->isCancelled() === true) {
            return $this;
        }

        $xmlStreamWriter->writeEndElement(); // end document
        $this->session->putValue('net.stubbles.webapp.lastRequestResponseData',
                                 $xmlStreamWriter->asXML()
        );
        $this->response->replaceBody($this->xmlTransformer->transform($xmlStreamWriter,
                                                                      $this->route->getProperty('name')
                                                            )
        );
        return $this;
    }

    /**
     * operations to be done after the request was processed
     *
     * @return  stubProcessor
     */
    public function cleanup()
    {
        $this->xmlGeneratorFacade->cleanup();
        return $this;
    }
}
?>