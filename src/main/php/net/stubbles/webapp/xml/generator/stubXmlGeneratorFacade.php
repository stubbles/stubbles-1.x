<?php
/**
 * Facade to hide complexity of connecting single generator instances.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubXmlGeneratorFacade.php 3173 2011-08-26 12:18:58Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::webapp::xml::generator::stubXmlGenerator'
);
/**
 * Facade to hide complexity of connecting single generator instances.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @since       1.5.0
 */
class stubXmlGeneratorFacade extends stubBaseObject implements stubXmlGenerator
{
    /**
     * request instance
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * injector instance
     *
     * @var  stubInjector
     */
    protected $injector;
    /**
     * list of xml generators to be used
     *
     * @var  array<stubXMLGenerator>
     */
    protected $xmlGenerators  = array();

    /**
     * constructor
     *
     * @param  stubRequest   $request
     * @param  stubInjector  $injector
     * @Inject
     */
    public function  __construct(stubRequest $request, stubInjector $injector)
    {
        $this->request  = $request;
        $this->injector = $injector;
    }

    /**
     * operations to be done before serialization is done
     */
    public function startup()
    {
        foreach ($this->injector->getConstant('net.stubbles.webapp.xml.generators') as $xmlGeneratorClassName) {
            $xmlGenerator = $this->injector->getInstance($xmlGeneratorClassName);
            $xmlGenerator->startup();
            $this->xmlGenerators[] = $xmlGenerator;
        }
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        foreach ($this->xmlGenerators as $xmlGenerator) {
            if ($xmlGenerator->isCachable() === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        $cacheVars = array();
        foreach ($this->xmlGenerators as $xmlGenerator) {
            $cacheVars = array_merge($cacheVars, $xmlGenerator->getCacheVars());
        }

        return $cacheVars;
    }

    /**
     * serializes something
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer)
    {
        foreach ($this->xmlGenerators as $xmlGenerator) {
            $xmlGenerator->generate($xmlStreamWriter, $xmlSerializer);
            if ($this->request->isCancelled() === true) {
                return;
            }
        }
    }

    /**
     * operations to be done after serialization is done
     */
    public function cleanup()
    {
        foreach ($this->xmlGenerators as $xmlGenerator) {
            $xmlGenerator->cleanup();
        }
    }
}
?>
