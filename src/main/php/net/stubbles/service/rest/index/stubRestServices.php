<?php
/**
 * Contains a list of all available rest services.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 */
stubClassLoader::load('net::stubbles::service::rest::index::stubRestService');
/**
 * Contains a list of all available rest services.
 *
 * @package     stubbles
 * @subpackage  service_rest_index
 * @since       1.8.0
 * @XMLTag(tagName='api')
 */
class stubRestServices extends stubBaseObject
{
    /**
     * current runtime mode
     *
     * @var  string
     */
    private $environment = 'n/a';
    /**
     * list of routes
     *
     * @var  array<stubRestService>
     */
    private $services    = array();

    /**
     * sets name of current environment
     *
     * @param   string            $environment
     * @return  stubRestServices
     */
    public function setEnvironmentName($environment)
    {
        $this->environment = $environment;
        return $this;
    }

    /**
     * returns current runtime mode
     *
     * @XMLTag(tagName='environment')
     * @return  stubMode
     */
    public function getEnvironment()
    {
        return array('name' => $this->environment);
    }

    /**
     * adds route to list
     *
     * @param   stubRestService  $service
     * @return  stubRestService
     */
    public function addService(stubRestService $service)
    {
        $this->services[] = $service;
        return $service;
    }

    /**
     * returns all routes
     *
     * @XMLTag(tagName='services')
     * @return  array<stubRestService>
     */
    public function getServices()
    {
        return $this->services;
    }
}
?>