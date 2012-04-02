<?php
/**
 * Base implementation for an encoder/decoder that decorates other en-/decoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubAbstractDecoratedStringEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubMethodNotSupportedException',
                      'net::stubbles::php::string::stubAbstractStringEncoder'
);
/**
 * Base implementation for an encoder/decoder that decorates other en-/decoders.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
abstract class stubAbstractDecoratedStringEncoder extends stubAbstractStringEncoder
{
    /**
     * the decorated encoder
     *
     * @var  stubStringEncoder
     */
    protected $encoder;

    /**
     * constructor
     *
     * @param  stubStringEncoder  $encoder  the encoder to apply recursively
     */
    public function __construct(stubStringEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * redirect method calls for non-existing methods to the decorated encoder
     *
     * @param   string  $method     the method to call
     * @param   array   $arguments  the arguments for the method call
     * @return  mixed   return value of the method call
     * @throws  stubMethodNotSupportedException
     */
    public function __call($method, $arguments)
    {
        if (is_callable(array($this->encoder, $method)) === true) {
            return call_user_func_array(array($this->encoder, $method), $arguments);
        }
        
        throw new stubMethodNotSupportedException('The method ' . $method . ' is not supported by ' . $this->encoder->getClassName());
    }
}
?>