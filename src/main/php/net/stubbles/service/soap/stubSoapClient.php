<?php
/**
 * Interface for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubSoapClient.php 2437 2010-01-05 22:22:26Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapClientConfiguration',
                      'net::stubbles::service::soap::stubSoapException'
);
/**
 * Interface for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
interface stubSoapClient extends stubObject
{
    /**
     * returns the configuration
     *
     * @return  stubSoapClientConfiguration
     */
    public function getConfig();

    /**
     * checks whether the client supports WSDL or not
     *
     * @return  bool
     */
    public function supportsWsdl();

    /**
     * returns a list of functions provided by the soap service
     *
     * @return  array<string>
     */
    public function getFunctions();

    /**
     * returns a list of types the soap service uses for interaction
     *
     * @return  array<string>
     */
    public function getTypes();

    /**
     * sets the timeout for soap calls using this client in seconds
     *
     * @param   int             $timeout
     * @return  stubSoapClient
     * @since   1.1.0
     */
    public function timeout($timeout);

    /**
     * invoke method call
     *
     * @param   string  $method  name of method to invoke
     * @param   array   $args    list of arguments for method
     * @return  mixed
     * @throws  stubSoapException
     */
    public function invoke($method, array $args = array());

    /**
     * returns data about last invoke() call usable for debugging
     *
     * @return  array<string,mixed>
     */
    public function getDebugData();
}
?>