<?php
/**
 * Exception to be thrown on failed SOAP operations.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubSoapException.php 2142 2009-03-27 13:47:27Z mikey $
 */
stubClassLoader::load('net::stubbles::service::soap::stubSoapFault');
/**
 * Exception to be thrown on failed SOAP operations.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
class stubSoapException extends stubException
{
    /**
     * information about the error
     *
     * @var  stubSoapFault
     */
    protected $soapFault;

    /**
     * constructor
     *
     * @param  stubSoapFault  $soapFault
     */
    public function __construct(stubSoapFault $soapFault)
    {
        $this->soapFault = $soapFault;
        parent::__construct($soapFault->getFaultString());
    }

    /**
     * returns more information about the error
     *
     * @return  stubSoapFault
     */
    public function getSoapFault()
    {
        return $this->soapFault;
    }
}
?>