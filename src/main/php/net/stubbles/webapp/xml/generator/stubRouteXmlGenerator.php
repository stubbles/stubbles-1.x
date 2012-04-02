<?php
/**
 * Serializes route data into xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubRouteXmlGenerator.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::stubRequestPrefixDecorator',
                      'net::stubbles::webapp::xml::route::stubRoute',
                      'net::stubbles::webapp::xml::route::stubXmlFormProcessable',
                      'net::stubbles::webapp::xml::generator::stubXmlGenerator'
);
/**
 * Serializes route data into xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 */
class stubRouteXmlGenerator extends stubBaseObject implements stubXmlGenerator
{
    /**
     * request instance to be used
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * injector instance to be used
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * route to be serialized
     *
     * @var  stubRoute
     */
    protected $route;
    /**
     * list of available processables
     *
     * @var  array<string,stubProcessable>
     */
    protected $processables = array();
    /**
     * switch whether document part is cachable or not
     *
     * @var  bool
     */
    protected $isCachable   = true;
    /**
     * list of cache variables for this route
     *
     * @var  array<string,scalar>
     */
    protected $cacheVars    = array();

    /**
     * constructor
     *
     * @param  stubRequest   $request
     * @param  stubInjector  $injector
     * @param  stubRoute     $route
     * @Inject
     */
    public function __construct(stubRequest $request, stubInjector $injector, stubRoute $route)
    {
        $this->request  = $request;
        $this->injector = $injector;
        $this->route    = $route;
    }

    /**
     * operations to be done before serialization is done
     */
    public function startup()
    {
        foreach ($this->route->getProcessables() as $name => $processable) {
            $processable = $this->injector->getInstance($processable)
                                          ->setContext(array('prefix' => $name));
            $processable->startup();
            if ($processable->isAvailable() === true) {
                $this->processables[$name] = $processable;
                // we can spare this if the route is not cachable
                if (true === $this->isCachable) {
                    if ($processable->isCachable() === false) {
                        $this->isCachable = false;
                    } else {
                        $this->cacheVars = array_merge($this->cacheVars, $processable->getCacheVars());
                    }
                }
            }
        }
    }

    /**
     * checks whether document part is cachable or not
     *
     * Document part is cachable if all processables are cachable.
     *
     * @return  bool
     */
    public function isCachable()
    {
        return $this->isCachable;
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return $this->cacheVars;
    }

    /**
     * serializes session data into result document
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer)
    {
        $formValues = array();
        foreach ($this->processables as $name => $processable) {
            $data = $processable->process();
            if ($this->request->isCancelled() === true) {
                return;
            }
            
            $xmlSerializer->serialize($data, $xmlStreamWriter, $name);
            if ($processable instanceof stubXmlFormProcessable) {
                $formValues[$name] = $processable->getFormValues();
            }
        }

        $xmlSerializer->serialize($formValues, $xmlStreamWriter, 'forms');
    }

    /**
     * operations to be done after serialization is done
     */
    public function cleanup()
    {
        foreach ($this->processables as $name => $processable) {
            $processable->cleanup();
        }
    }
}
?>
