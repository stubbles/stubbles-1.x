<?php
/**
 * Implementation of a SOAP client using PHP's native SOAPClient class.
 *
 * @package     stubbles
 * @subpackage  service_soap_native
 * @version     $Id: stubNativeSoapClient.php 2437 2010-01-05 22:22:26Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::service::soap::stubAbstractSoapClient',
                      'net::stubbles::service::soap::stubSoapFault'
);
/**
 * Implementation of a SOAP client using PHP's native SOAPClient class.
 *
 * @package     stubbles
 * @subpackage  service_soap_native
 * @see         http://php.net/soap
 */
class stubNativeSoapClient extends stubAbstractSoapClient
{
    /**
     * constructor
     *
     * @param   stubSoapClientConfiguration  $config
     * @throws  stubRuntimeException
     * @throws  stubIllegalArgumentException
     */
    public function __construct(stubSoapClientConfiguration $config)
    {
        if (extension_loaded('soap') === false) {
            throw new stubRuntimeException('net::stubbles::service::soap::native::stubNativeSoapClient requires PHP extension soap.');
        }
        
        $version = $config->getVersion();
        if (null !== $version && SOAP_1_1 !== $version && SOAP_1_2 !== $version) {
            throw new stubIllegalArgumentException('Configuration error: version must be one of SOAP_1_1 or SOAP_1_2.');
        }
        
        $requestStyle = $config->getRequestStyle();
        if (null === $requestStyle) {
            $config->setRequestStyle(SOAP_RPC);
        } elseif (SOAP_RPC !== $requestStyle && SOAP_DOCUMENT !== $requestStyle) {
            throw new stubIllegalArgumentException('Configuration error: request style must be one of SOAP_RPC or SOAP_DOCUMENT.');
        }
        
        $usage = $config->getUsage();
        if (null === $usage) {
            $config->setUsage(SOAP_ENCODED);
        } elseif (SOAP_ENCODED !== $usage && SOAP_LITERAL !== $usage) {
            throw new stubIllegalArgumentException('Configuration error: usage must be one of SOAP_ENCODED or SOAP_LITERAL.');
        }
        
        parent::__construct($config);
    }

    /**
     * checks whether the client supports WSDL or not
     *
     * @return  bool
     */
    public function supportsWsdl()
    {
        return true;
    }

    /**
     * returns a list of functions provided by the soap service
     *
     * @return  array<string>
     */
    public function getFunctions()
    {
        return $this->createClient()->__getFunctions();
    }

    /**
     * returns a list of types the soap service uses for interaction
     *
     * @return  array<string>
     */
    public function getTypes()
    {
        return $this->createClient()->__getTypes();
    }

    /**
     * invoke method call
     *
     * Options for the invocation handling include:
     *  - asParameters: if set to true the parameters will be passed as array under the given key
     *  - parseFromStdClass: if set this will be used as name of the stdClass property to parse result from
     *
     * @param   string  $method   name of method to invoke
     * @param   array   $args     optional  list of arguments for method
     * @param   array   $options  optional  options for the invocation handling
     * @return  mixed
     * @throws  stubSoapException
     */
    public function invoke($method, array $args = array(), array $options = array())
    {
        $client = $this->createClient();
        if (isset($options['asParameters']) === false) {
            $args = array_values($args);
        } else {
            $args = array($options['asParameters'] => $args);
        }
        
        $result          = $client->__soapCall($method, $args);
        $this->debugData = array('endPoint'           => $this->config->getEndPoint()->get(true),
                                 'usedWsdl'           => $this->config->usesWsdl(),
                                 'lastMethod'         => $method,
                                 'lastArgs'           => $args,
                                 'lastRequestHeader'  => $client->__getLastRequestHeaders(),
                                 'lastRequest'        => $client->__getLastRequest(),
                                 'lastResponseHeader' => $client->__getLastResponseHeaders(),
                                 'lastResponse'       => $client->__getLastResponse()
                           );
        if (is_soap_fault($result) === false) {
            if (isset($options['parseFromStdClass']) === true) {
                $propertyName = $options['parseFromStdClass'];
                if ($result instanceof stdClass && isset($result->$propertyName) === true) {
                    return $result->$propertyName;
                }
            }
            
            return $result;
        }
        
        throw new stubSoapException(new stubSoapFault($result->faultcode,
                                                      $result->faultstring,
                                                      (isset($result->faultactor) === false) ? (null) : ($result->faultactor),
                                                      (isset($result->detail) === false) ? (null) : ($result->detail)
                                        )
                  );
    }

    /**
     * helper method to create the client
     *
     * @return  SoapClient
     */
    protected function createClient()
    {
        $options = array('exceptions'         => false,
                         'trace'              => true,
                         'encoding'           => $this->config->getDataEncoding(),
                         'connection_timeout' => $this->timeout
                   );
        if (null !== $this->config->getVersion()) {
            $options['version'] = $this->config->getVersion();
        }
        
        if ($this->config->hasClassMapping() === true) {
            $options['classmap'] = $this->config->getClassMapping();
        }
        
        $endPoint = $this->config->getEndPoint();
        $user     = $endPoint->getUser();
        if (null !== $user) {
            $options['login'] = $user;
        }
        
        $password = $endPoint->getPassword();
        if (null !== $password) {
            $options['password'] = $password;
        }
        
        if (true === $this->config->usesWsdl()) {
            if ($this->config->hasLocation() === true) {
                $options['location'] = $this->config->getLocation()->get(true);
            }
            
            $client = $this->createClientInstance($endPoint->get(true), $options);
        } else {
            $options['location'] = $endPoint->get(true);
            $options['uri']      = $this->config->getUri();
            $options['style']    = $this->config->getRequestStyle();
            $options['use']      = $this->config->getUsage();
            $client              = $this->createClientInstance(null, $options);
        }
        
        return $client;
    }

    /**
     * creates the client
     *
     * @param   string               $url
     * @param   array<string,mixed>  $options
     * @return  SoapClient
     */
    // @codeCoverageIgnoreStart
    protected function createClientInstance($url, $options)
    {
        return new SoapClient($url, $options);
    }
    // @codeCoverageIgnoreEnd
}
?>