<?php
/**
 * Interface for string encoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubStringEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubMethodNotSupportedException');
/**
 * Interface for string encoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
interface stubStringEncoder extends stubObject
{
    /**
     * the mode to apply onto the string: encode the string
     *
     * @see  apply()
     */
    const MODE_ENCODE = 1;
    /**
     * the mode to apply onto the string: decode the string
     *
     * @see  apply()
     */
    const MODE_DECODE = 2;

    /**
     * applies the encoder with the given mode
     *
     * A MethodNotSupportedException is thrown in case the encoder does not
     * support decoding a string.
     *
     * @param   string  $string
     * @param   int     $mode
     * @return  string
     * @throws  stubMethodNotSupportedException
     */
    public function apply($string, $mode);

    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string);

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     * @throws  stubMethodNotSupportedException
     */
    public function decode($string);

    /**
     * checks whether an encoding is reversible or not
     *
     * @return  bool
     */
    public function isReversible();
}
?>