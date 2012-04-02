<?php
/**
 * Encoder/decoder for URLs.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubURLEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractStringEncoder');
/**
 * Encoder/decoder for URLs.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
class stubURLEncoder extends stubAbstractStringEncoder
{
    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        return urlencode($string);
    }

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function decode($string)
    {
        return urldecode($string);
    }
}
?>