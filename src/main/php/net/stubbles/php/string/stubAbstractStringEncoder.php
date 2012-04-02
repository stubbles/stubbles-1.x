<?php
/**
 * Abstract base implementation for string encoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubAbstractStringEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::string::stubStringEncoder'
);
/**
 * Abstract base implementation for string encoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
abstract class stubAbstractStringEncoder extends stubBaseObject implements stubStringEncoder
{
    /**
     * applies the encoder with the given mode
     *
     * A MethodNotSupportedException is thrown in case the encoder does not
     * support decoding a string.
     *
     * @param   string  $string
     * @param   int     $mode
     * @return  string
     * @throws  stubIllegalArgumentException
     */
    public function apply($string, $mode)
    {
        switch ($mode) {
            case stubStringEncoder::MODE_ENCODE:
                return $this->encode($string);
            
            case stubStringEncoder::MODE_DECODE:
                return $this->decode($string);
            
            default:
                throw new stubIllegalArgumentException('Invalid mode.');
        }
    }

    /**
     * checks whether an encoding is reversible or not
     *
     * @return  bool
     */
    public function isReversible()
    {
        return true;
    }
}
?>