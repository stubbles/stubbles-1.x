<?php
/**
 * Container for SOAP fault data.
 *
 * @package     stubbles
 * @subpackage  service_soap
 * @version     $Id: stubSoapFault.php 2142 2009-03-27 13:47:27Z mikey $
 */
/**
 * Container for SOAP fault data.
 *
 * @package     stubbles
 * @subpackage  service_soap
 */
class stubSoapFault extends stubBaseObject
{
    /**
     * error code
     *
     * @var  string
     */
    protected $faultCode;
    /**
     * error message
     *
     * @var  string
     */
    protected $faultString;
    /**
     * what caused the error
     *
     * @var  string
     */
    protected $faultActor;
    /**
     * more details about the error
     *
     * @var  mixed
     */
    protected $detail;

    /**
     * constructor
     *
     * @param  string  $faultCode    error code
     * @param  string  $faultString  error message
     * @param  string  $faultActor   optional  what caused the error
     * @param  mixed   $detail       optional  more details about the error
     */
    public function __construct($faultCode, $faultString, $faultActor = null, $detail = null)
    {
        $this->faultCode   = $faultCode;
        $this->faultString = $faultString;
        $this->faultActor  = $faultActor;
        $this->detail      = $detail;
    }

    /**
     * returns the error code
     *
     * @return  string
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }

    /**
     * returns the error message
     *
     * @return  string
     */
    public function getFaultString()
    {
        return $this->faultString;
    }

    /**
     * returns what caused the error
     *
     * @return  string
     */
    public function getFaultActor()
    {
        return $this->faultActor;
    }

    /**
     * returns more details about the error
     *
     * @return  mixed
     */
    public function getDetail()
    {
        return $this->detail;
    }
}
?>