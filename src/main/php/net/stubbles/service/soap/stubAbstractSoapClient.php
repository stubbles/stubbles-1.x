<?php
/**
 * Basic implementation for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubAbstractSoapClient.php 2437 2010-01-05 22:22:26Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapClient');
/**
 * Basic implementation for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
abstract class stubAbstractSoapClient extends stubBaseObject implements stubSoapClient
{
    /**
     * configuration data for client
     *
     * @var  stubSoapClientConfiguration
     */
    protected $config;
    /**
     * timeout for soap calls in seconds
     *
     * @var  int
     */
    protected $timeout   = 2;
    /**
     * debug data for last call to invoke()
     *
     * @var  array<string,mixed>
     */
    protected $debugData = array();

    /**
     * constructor
     *
     * @param  stubSoapClientConfiguration  $config
     */
    public function __construct(stubSoapClientConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * returns the configuration
     *
     * @return  stubSoapClientConfiguration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * sets the timeout for soap calls using this client in seconds
     *
     * @param   int             $timeout
     * @return  stubSoapClient
     * @since   1.1.0
     */
    public function timeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * returns data about last invoke() call usable for debugging
     *
     * @return  array<string,mixed>
     */
    public function getDebugData()
    {
        return $this->debugData;
    }
}
?>