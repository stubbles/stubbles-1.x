<?php
/**
 * Binding module for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap_ioc
 * @version     $Id: stubSoapBindingModule.php 2882 2011-01-11 20:54:26Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::module::stubBindingModule');
/**
 * Binding module for SOAP clients.
 *
 * @package     stubbles
 * @subpackage  service_soap_ioc
 */
class stubSoapBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * list of available drivers
     *
     * @var  array<string,string|ReflectionClass>
     */
    protected $clients = array();

    /**
     * static constructor
     *
     * @param   array<string,string|ReflectionClass>  $clients
     * @return  stubSoapBindingModule
     */
    public static function create(array $clients)
    {
        $self = new self();
        $self->clients = $clients;
        return $self;
    }

    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        if (count($this->clients) === 0) {
            // initialize lazy only of required
            $binder->bind('stubSoapClientGenerator')
                   ->to('net::stubbles::service::soap::stubSoapClientGenerator')
                   ->asSingleton();
        } else {
            stubClassLoader::load('net::stubbles::service::soap::stubSoapClientGenerator');
            $soapClientGenerator = new stubSoapClientGenerator();
            $soapClientGenerator->setAvailableClients($this->clients);
            $binder->bind('stubSoapClientGenerator')
                   ->toInstance($soapClientGenerator);
        }
        
    }
}
?>