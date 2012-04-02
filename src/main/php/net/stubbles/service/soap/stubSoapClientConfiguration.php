<?php
/**
 * Configuration container for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubSoapClientConfiguration.php 2149 2009-03-30 20:02:46Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::peer::http::stubHTTPURL'
);
/**
 * Configuration container for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
class stubSoapClientConfiguration extends stubBaseObject
{
    /**
     * the url where the SOAP service can be reached at
     *
     * @var  stubHTTPURLContainer
     */
    protected $endPoint;
    /**
     * the uri to use
     *
     * @var  string
     */
    protected $uri;
    /**
     * real location if wsdl does not contain the real location url to call
     *
     * @var  stubHTTPURLContainer
     */
    protected $location;
    /**
     * whether to use WSDL mode or not
     *
     * @var  bool
     */
    protected $useWsdl      = true;
    /**
     * the SOAP version to use
     *
     * @var  string
     */
    protected $version      = null;
    /**
     * encoding of the data
     *
     * @var  string
     */
    protected $dataEncoding = 'iso-8859-1';
    /**
     * style of the request
     *
     * @var  string
     */
    protected $requestStyle = null;
    /**
     * the usage
     *
     * @var  string
     */
    protected $usage        = null;
    /**
     * list of wsdl type to class mapping
     *
     * @var  array<string,string>
     */
    protected $classMapping = array();

    /**
     * constructor
     *
     * @param   string|stubHTTPURLContainer  $endPoint
     * @param   string                       $uri
     * @throws  stubIllegalArgumentException
     */
    public function __construct($endPoint, $uri)
    {
        if (is_string($endPoint) === true) {
            $endPoint = stubHTTPURL::fromString($endPoint);
            if (null === $endPoint) {
                throw new stubIllegalArgumentException('Endpoint must be a string denoting an URL or an instance of net::stubbles::peer::http::stubHTTPURLContainer.');
            }
        } elseif (($endPoint instanceof stubHTTPURLContainer) === false) {
            throw new stubIllegalArgumentException('Endpoint must be a string denoting an URL or an instance of net::stubbles::peer::http::stubHTTPURLContainer.');
        }
        
        $this->endPoint = $endPoint;
        $this->uri      = $uri;
    }

    /**
     * static constructor
     *
     * @param   string|stubHTTPURLContainer  $endPoint
     * @param   string                       $uri
     * @return  stubSoapClientConfiguration
     */
    public static function create($endPoint, $uri)
    {
        return new self($endPoint, $uri);
    }

    /**
     * returns url where the SOAP service can be reached at
     *
     * @return  stubHTTPURLContainer
     */
    public function getEndpoint()
    {
        return $this->endPoint;
    }

    /**
     * returns the uri to use
     *
     * @return  string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * sets the real location
     *
     * @param   string|stubHTTPURLContainer $location
     * @return  stubSoapClientConfiguration
     * @throws  stubIllegalArgumentException
     */
    public function setLocation($location)
    {
        if (is_string($location) === true) {
            $location = stubHTTPURL::fromString($location);
            if (null === $location) {
                throw new stubIllegalArgumentException('Location must be a string denoting an URL or an instance of net::stubbles::peer::http::stubHTTPURLContainer.');
            }
        } elseif (($location instanceof stubHTTPURLContainer) === false) {
            throw new stubIllegalArgumentException('Location must be a string denoting an URL or an instance of net::stubbles::peer::http::stubHTTPURLContainer.');
        }
        
        $this->location = $location;
        return $this;
    }

    /**
     * checks if a location is set
     *
     * @return  bool
     */
    public function hasLocation()
    {
        return (null != $this->location);
    }

    /**
     * returns location
     *
     * @return  stubHTTPURLContainer
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * switch whether to use WSDL mode or not
     *
     * @param   bool                         $useWsdl
     * @return  stubSoapClientConfiguration
     */
    public function useWsdl($useWsdl)
    {
        $this->useWsdl = (bool) $useWsdl;
        return $this;
    }

    /**
     * checks whether to use WSDL mode or not
     *
     * @return  bool
     */
    public function usesWsdl()
    {
        return $this->useWsdl;
    }

    /**
     * sets the SOAP version
     *
     * @param   string                       $version
     * @return  stubSoapClientConfiguration
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * returns the SOAP version
     *
     * @return  string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * sets the encoding of the data
     *
     * @param   string                       $dataEncoding
     * @return  stubSoapClientConfiguration
     */
    public function setDataEncoding($dataEncoding)
    {
        $this->dataEncoding = $dataEncoding;
        return $this;
    }

    /**
     * returns the encoding of the data
     *
     * @return  string
     */
    public function getDataEncoding()
    {
        return $this->dataEncoding;
    }

    /**
     * sets the style of the request
     *
     * @param   string                       $requestStyle
     * @return  stubSoapClientConfiguration
     */
    public function setRequestStyle($requestStyle)
    {
        $this->requestStyle = $requestStyle;
        return $this;
    }

    /**
     * returns the style of the request
     *
     * @return  string
     */
    public function getRequestStyle()
    {
        return $this->requestStyle;
    }

    /**
     * sets the usage
     *
     * @param   string                       $usage
     * @return  stubSoapClientConfiguration
     */
    public function setUsage($usage)
    {
        $this->usage = $usage;
        return $this;
    }

    /**
     * returns the usage
     *
     * @return  string
     */
    public function getUsage()
    {
        return $this->usage;
    }

    /**
     * registers a class mapping
     *
     * @param   string                       $wsdlType
     * @param   ReflectionClass              $class
     * @return  stubSoapClientConfiguration
     */
    public function registerClassMapping($wsdlType, ReflectionClass $class)
    {
        $this->classMapping[$wsdlType] = $class->getName();
        return $this;
    }

    /**
     * checks whether at least one class mapping exists
     *
     * @return  bool
     */
    public function hasClassMapping()
    {
        return (count($this->classMapping) > 0);
    }

    /**
     * returns the class mapping
     *
     * @return  array<string,string>
     */
    public function getClassMapping()
    {
        return $this->classMapping;
    }
}
?>