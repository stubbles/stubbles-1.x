<?php
/**
 * Exception to be thrown when an error on a network connection occurs.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubConnectionException.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubChainedException');
/**
 * Exception to be thrown when an error on a network connection occurs.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubConnectionException extends stubChainedException
{
    // intentionally empty
}
?>