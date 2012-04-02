<?php
/**
 * Serializes request data into xml result document.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 * @version     $Id: stubRequestXmlGenerator.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::request::useragent::stubUserAgent',
                      'net::stubbles::webapp::xml::generator::stubXmlGenerator'
);
/**
 * Serializes request data into xml result document.
 *
 * Currently this is the user agent and the request errors created during
 * processing of the processabled:
 * <code>
 * <document>
 *   [...]
 *   <request>
 *     <userAgent name="Mozilla ..." isBot="false"/>
 *     <errors>
 *       <error id="foo">
 *         <messages>
 *           <de_DE>Dies ist eine deutsche Fehlermeldung.</de_DE>
 *           <en_EN>This is an english error message.</en_EN>
 *         </messages>
 *       </error>
 *       [...]
 *     </errors>
 *   </request>
 *   [...]
 * </document>
 * </code>
 * Concrete request values will not be written into the result document.
 *
 * The serializing of the request should take place after processables were
 * processed - only these generate the request value errors stored in the
 * request. Additionally those processables should take care of whether a route
 * is cachable or not and the required cache variables.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator
 */
class stubRequestXmlGenerator extends stubBaseObject implements stubXmlGenerator
{
    /**
     * request instance to be used
     *
     * @var  stubRequest
     */
    protected $request;
    /**
     * user agent detected from request
     *
     * @var    stubUserAgent
     * @since  1.2.0
     */
    protected $userAgent;

    /**
     * constructor
     *
     * @param  stubRequest    $request
     * @param  stubUserAgent  $userAgent
     * @Inject
     */
    public function __construct(stubRequest $request, stubUserAgent $userAgent)
    {
        $this->request   = $request;
        $this->userAgent = $userAgent;
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
        return array('isBot' => $this->userAgent->isBot());
    }

    /**
     * serializes request data into result document
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  writer to be used
     * @param  stubXMLSerializer    $xmlSerializer    serializer to be used
     */
    public function generate(stubXMLStreamWriter $xmlStreamWriter, stubXMLSerializer $xmlSerializer)
    {
        $xmlStreamWriter->writeStartElement('request');
        $xmlSerializer->serialize($this->userAgent, $xmlStreamWriter);
        foreach ($this->request->paramErrors()->get() as $requestValueName => $requestErrorValues) {
            $xmlStreamWriter->writeStartElement('value');
            $xmlStreamWriter->writeAttribute('name', $requestValueName);
            $xmlSerializer->serialize(array_values($requestErrorValues), $xmlStreamWriter, 'errors');
            $xmlStreamWriter->writeEndElement();
        }

        $xmlStreamWriter->writeEndElement();  // end request
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
