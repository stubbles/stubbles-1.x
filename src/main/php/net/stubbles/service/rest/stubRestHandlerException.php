<?php
/**
 * Exception to be thrown if something goes wrong while handling a ReST request.
 *
 * @package     stubbles
 * @subpackage  service_rest
 * @version     $Id: stubRestHandlerException.php 2403 2009-12-04 15:16:53Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown if something goes wrong while handling a ReST request.
 *
 * @package     stubbles
 * @subpackage  service_rest
 */
class stubRestHandlerException extends stubChainedException
{
    /**
     * status code
     *
     * @var  int
     */
    protected $statusCode;
    /**
     * status message
     *
     * @var  string
     */
    protected $statusMessage;

    /**
     * constructor
     *
     * @param  int               $statusCode     status code
     * @param  string            $statusMessage  status message
     * @param  string|Exception  $cause          exception that caused this exception or a message
     */
    public function __construct($statusCode, $statusMessage, $cause)
    {
        $this->statusCode    = $statusCode;
        $this->statusMessage = $statusMessage;
        if ($cause instanceof Exception) {
            parent::__construct($cause->getMessage(), $cause);
        } else {
            parent::__construct($cause);
        }
    }

    /**
     * returns status code
     *
     * @return  int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * returns status message
     *
     * @return  string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }
}
?>