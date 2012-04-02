<?php
/**
 * Factory for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubSoapClientGenerator.php 2463 2010-01-18 15:38:00Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubRuntimeException',
                      'net::stubbles::reflection::stubReflectionClass',
                      'net::stubbles::service::soap::stubSoapClient',
                      'net::stubbles::service::soap::stubSoapClientConfiguration'
);
/**
 * Factory for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
class stubSoapClientGenerator extends stubBaseObject
{
    /**
     * list of available drivers
     *
     * @var  array<string,string|ReflectionClass>
     */
    protected $clients = array();

    /**
     * constructor
     */
    public function __construct()
    {
        if (extension_loaded('soap') === true) {
            $this->clients['net::stubbles::service::soap::native::stubNativeSoapClient'] = 'net::stubbles::service::soap::native::stubNativeSoapClient';
        }
    }

    /**
     * adds a client to list of drivers
     *
     * @param   ReflectionClass  $clientClass
     * @throws  stubIllegalArgumentException
     */
    public function addClient(ReflectionClass $clientClass)
    {
        if ($clientClass->implementsInterface('stubSoapClient') === false) {
            throw new stubIllegalArgumentException('Client class must implement interface net::stubbles::service::soap::stubSoapClient.');
        }
        
        $this->clients[$clientClass->getName()] = $clientClass;
    }

    /**
     * sets the available clients
     *
     * @param  array<string,string|ReflectionClass>  $clients
     */
    public function setAvailableClients(array $clients)
    {
        $this->clients = $clients;
    }

    /**
     * returns a list of available clients
     *
     * @return  array<string,string|ReflectionClass>
     */
    public function getAvailableClients()
    {
        return $this->clients;
    }

    /**
     * removes a client class from list of drivers
     *
     * @param  string  $clientClassName
     */
    public function removeClient($clientClassName)
    {
        if (isset($this->clients[$clientClassName]) === true) {
            unset($this->clients[$clientClassName]);
        }
    }

    /**
     * creates a client for given config
     *
     * If no valid or suitable client can be found a stubRuntimeException will
     * be thrown.
     *
     * @param   stubSoapClientConfiguration  $config
     * @param   bool                         $mustUseWsdl  optional
     * @return  stubSoapClient
     * @throws  stubRuntimeException
     */
    public function forConfig(stubSoapClientConfiguration $config, $mustUseWsdl = false)
    {
        foreach ($this->clients as $clientClass) {
            if (is_string($clientClass) === true) {
                $clientClass = new stubReflectionClass($clientClass);
            }
            
            try {
                if ($clientClass->getConstructor() !== null && $clientClass->getConstructor()->getNumberOfParameters() > 0) {
                    $client = $clientClass->newInstanceArgs(array($config));
                } else {
                    $client = $clientClass->newInstance();
                }
            } catch (Exception $e) {
                continue;
            }
            
            if (true === $mustUseWsdl && $client->supportsWsdl() === false && $config->usesWsdl() === true) {
                continue;
            }
            
            return $client;
        }
        
        throw new stubRuntimeException('No suitable SOAP client found.');
    }
}
?>