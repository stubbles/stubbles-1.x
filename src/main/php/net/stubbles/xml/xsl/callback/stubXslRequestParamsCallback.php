<?php
/**
 * Class to transfer the query string into an xml document.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslRequestParamsCallback.php 2972 2011-02-07 18:32:07Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::xml::xsl::callback::stubXslAbstractCallback'
);
/**
 * Class to transfer the query string into an xml document.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
class stubXslRequestParamsCallback extends stubXslAbstractCallback
{
    /**
     * request instance
     *
     * @var  stubRequest
     */
    protected $request;

    /**
     * constructor
     *
     * @param  stubXMLStreamWriter  $xmlStreamWriter  xml stream writer to create the document with
     * @param  stubRequest          $request
     * @Inject
     */
    public function __construct(stubXMLStreamWriter $xmlStreamWriter, stubRequest $request)
    {
        parent::__construct($xmlStreamWriter);
        $this->request = $request;
    }

    /**
     * returns the query string within a dom document
     * 
     * @return  DOMDocument
     * @XslMethod
     */
    public function getQueryString()
    {
        $queryString = $this->request->readHeader('QUERY_STRING')->unsecure();
        $this->xmlStreamWriter->writeElement('requestParams',
                                             array(),
                                             $this->filterQueryString($queryString)
        );
        
        return $this->createDomDocument();
    }

    /**
     * filters processor and page out of query string
     *
     * @param   string  $queryString
     * @return  string
     */
    protected function filterQueryString($queryString)
    {
        $return = $queryString;
        $data   = array();
        parse_str($queryString, $data);
        foreach ($data as $key => $value) {
            if ('processor' === $key || 'page' === $key) {
                $return = str_replace($key . '=' . $value, '', $return);
            }
        }
        
        return str_replace('&=', '', str_replace('&&', '&', $return));
    }
}
?>