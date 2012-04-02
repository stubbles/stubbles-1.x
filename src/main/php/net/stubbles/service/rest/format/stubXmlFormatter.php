<?php
/**
 * ReST result formatters for XML.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @version     $Id: stubXmlFormatter.php 2436 2010-01-05 16:38:36Z mikey $
 */
stubClassLoader::load('net::stubbles::service::rest::format::stubErrorFormatter',
                      'net::stubbles::service::rest::format::stubFormatter',
                      'net::stubbles::xml::serializer::stubXmlSerializerFacade'
);
/**
 * ReST result formatters for XML.
 *
 * The XML formatter uses the XML serializer provided by Stubbles. This allows
 * to customize XML serialization of result objects with annotations from the
 * XML serializer package.
 *
 * @package     stubbles
 * @subpackage  service_rest_format
 * @since       1.1.0
 */
class stubXmlFormatter extends stubBaseObject implements stubFormatter, stubErrorFormatter
{
    /**
     * serializer to be used
     *
     * @var  stubXmlSerializerFacade
     */
    protected $xmlSerializerFacade;

    /**
     * constructor
     *
     * @param  stubXmlSerializerFacade  $xmlSerializerFacade
     * @Inject
     */
    public function __construct(stubXmlSerializerFacade $xmlSerializerFacade)
    {
        $this->xmlSerializerFacade = $xmlSerializerFacade;
    }

    /**
     * returns content type of formatted result
     *
     * @return  string
     */
    public function getContentType()
    {
        return 'text/xml';
    }

    /**
     * formats result for response
     *
     * @param   mixed   $result
     * @return  string
     */
    public function format($result)
    {
        return $this->xmlSerializerFacade->serializeToXml($result);
    }

    /**
     * write error message about 404 Not Found error
     *
     * @return  string
     */
    public function formatNotFoundError()
    {
        return $this->xmlSerializerFacade->serializeToXml(array('error' => 'Given resource could not be found.'));
    }

    /**
     * write error message about 405 Method Not Allowed error
     *
     * @param   string         $requestMethod   original request method
     * @param   array<string>  $allowedMethods  list of allowed methods
     * @return  string
     */
    public function formatMethodNotAllowedError($requestMethod, array $allowedMethods)
    {
        return $this->xmlSerializerFacade->serializeToXml(array('error' => 'The given request method ' . strtoupper($requestMethod) . ' is not valid. Please use ' . join(', ', $allowedMethods) . '.'));
    }

    /**
     * write error message about 500 Internal Server error
     *
     * @param   Exception  $e
     * @return  string
     */
    public function formatInternalServerError(Exception $e)
    {
        return $this->xmlSerializerFacade->serializeToXml(array('error' => 'Internal Server Error: ' . $e->getMessage()));
    }
}
?>