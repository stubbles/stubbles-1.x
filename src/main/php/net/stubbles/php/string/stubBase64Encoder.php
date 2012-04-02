<?php
/**
 * Encoder/decoder for base64.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubBase64Encoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractStringEncoder');
/**
 * Encoder/decoder for base64.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
class stubBase64Encoder extends stubAbstractStringEncoder
{
    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        return base64_encode($string);
    }

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function decode($string)
    {
        return base64_decode($string);
    }
}
?>