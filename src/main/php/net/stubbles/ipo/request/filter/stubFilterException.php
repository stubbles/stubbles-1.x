<?php
/**
 * Exception to be thrown when a method has been passed an illegal or
 * inappropriate argument.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_filter
 * @version     $Id: stubFilterException.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Exception to be thrown when a method has been passed an illegal or
 * inappropriate argument.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter
 */
class stubFilterException extends stubException
{
    /**
     * error
     *
     * @var  stubRequestValueError
     */
    protected $error;

    /**
     * Constructor for this class.
     *
     * @param  stubRequestValueError  $error  error
     */
    public function __construct(stubRequestValueError $error)
    {
        parent::__construct($error->getMessage('en_EN'));
        $this->error = $error;
    }

    /**
     * returns value that lead to error, but it is safe enough to use
     *
     * @return  stubRequestValueError  value that is safe enough to use, else null
     */
    public function getError()
    {
        return $this->error;
    }
}
?>