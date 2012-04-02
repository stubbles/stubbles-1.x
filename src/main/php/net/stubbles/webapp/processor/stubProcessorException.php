<?php
/**
 * Exception to be thrown if an error occurs while processor handling.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 * @version     $Id: stubProcessorException.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown if an error occurs while processor handling.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 */
class stubProcessorException extends stubChainedException
{
    /**
     * status code of processing failure
     *
     * @var  int
     */
    protected $statusCode;

    /**
     * constructor
     *
     * @param  int        $statusCode
     * @param  string     $message
     * @param  Exception  $cause
     */
    public function __construct($statusCode, $message, Exception $cause = null)
    {
        parent::__construct($message, $cause);
        $this->statusCode = $statusCode;
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
}
?>