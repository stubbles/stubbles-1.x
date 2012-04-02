<?php
/**
 * Generator for serializing current variant configuration to XML.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubVariantListGenerator.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::webapp::variantmanager::types::stubVariant',
                      'net::stubbles::webapp::xml::generator::stubXmlGenerator'
);
/**
 * Generator for serializing current variant configuration to XML.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 */
class stubVariantListGenerator extends stubBaseObject implements stubXmlGenerator
{
    /**
     * session instance to be used
     *
     * @var  stubSession
     */
    protected $session;
    /**
     * injector instance for creating the variant factory if necessary
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubRequest  $request
     * @param  stubSession  $session
     * @Inject
     */
    public function __construct(stubSession $session, stubInjector $injector)
    {
        $this->session  = $session;
        $this->injector = $injector;
    }

    /**
     * operations to be done before serialization is done
     */
    public function startup()
    {
        // nothing to do
    }

    /**
     * checks whether document part is cachable or not
     *
     * @return  bool
     */
    public function isCachable()
    {
        return true;
    }

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars()
    {
        return array();
    }

    /**
     * serializes session data into result document
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer)
    {
        $xmlStreamWriter->writeStartElement('variants');
        $xmlStreamWriter->writeStartElement('variantList');
        if ($this->session->hasValue('net.stubbles.webapp.variantmanager.variant.name') === true) {
            $this->serializeChildVariants($this->injector->getInstance('net::stubbles::webapp::variantmanager::stubVariantFactory')
                                                         ->getVariantsMap()
                                                         ->getRootVariant(),
                                          $xmlStreamWriter
            );
        }

        $xmlStreamWriter->writeEndElement();  // end variantList
        $xmlStreamWriter->writeEndElement();  // end variants
    }

    /**
     * serializes children of given variant
     *
     * @param  stubVariant          $variant
     * @param  stubXMLStreamWriter  $xmlStreamWriter
     */
    protected function serializeChildVariants(stubVariant $variant, stubXMLStreamWriter $xmlStreamWriter)
    {
         foreach ($variant->getChildren() as $childVariant) {
            /* @var $childVariant stubVariant */
            $xmlStreamWriter->writeStartElement('variant');
            $xmlStreamWriter->writeAttribute('name', $childVariant->getFullQualifiedName());
            $xmlStreamWriter->writeAttribute('title', $childVariant->getTitle());
            $xmlStreamWriter->writeAttribute('type', $childVariant->getClassName());
            $this->serializeChildVariants($childVariant, $xmlStreamWriter);
            $xmlStreamWriter->writeEndElement();
        }
    }

    /**
     * operations to be done after serialization is done
     */
    public function cleanup()
    {
        // nothing to do
    }
}
?>