<?php
/**
 * Encoder/decoder that decorates other en-/decoders and applies the en-/decoding
 * recursively if the value to en-/decode is an array or object.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubRecursiveStringEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractDecoratedStringEncoder');
/**
 * Encoder/decoder that decorates other en-/decoders and applies the en-/decoding
 * recursively if the value to en-/decode is an array or object.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
class stubRecursiveStringEncoder extends stubAbstractDecoratedStringEncoder
{
    /**
     * encodes a value recursively
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        if (is_scalar($string) === true) {
            return $this->encoder->encode($string);
        }
        
        if (is_array($string) === true) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->encode($val);
            }
        } elseif (is_object($string) === true) {
            foreach (get_object_vars($string) as $key => $val) {
                $string->$key = $this->encode($val);
            }
        }
        
        return $string;
    }

    /**
     * decodes a value recursively
     *
     * @param   string  $string
     * @return  string
     */
    public function decode($string)
    {
        if (is_scalar($string) === true) {
            return $this->encoder->decode($string);
        }
        
        if (is_array($string) === true) {
            foreach ($string as $key => $val) {
                $string[$key] = $this->decode($val);
            }
        } elseif (is_object($string) === true) {
            foreach (get_object_vars($string) as $key => $val) {
                $string->$key = $this->decode($val);
            }
        }
        
        return $string;
    }
}
?>