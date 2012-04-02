<?php
/**
 * Encoder for firebug console messages.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util
 * @version     $Id: stubFirebugEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubMethodNotSupportedException',
                      'net::stubbles::php::string::stubAbstractStringEncoder'
);
/**
 * Encoder for firebug console messages.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_util
 */
class stubFirebugEncoder extends stubAbstractStringEncoder
{
    /**
     * the debug level
     *
     * @var  string
     */
    protected $level = 'error';

    /**
     * sets the debug level
     *
     * @param  string  $level
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * returns the debug level
     *
     * @return  string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        $result = '';
        $lines  = explode("\n", $string);
        foreach ($lines as $line) {
            $result .= "console.{$this->level}('" . addslashes($line) . "');\n";
        }

        return $result;
    }

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     * @throws  stubMethodNotSupportedException
     */
    public function decode($string)
    {
        throw new stubMethodNotSupportedException('Decoding a firebug-encoded string is not supported.');
    }

    /**
     * checks whether an encoding is reversible or not
     *
     * @return  bool
     */
    public function isReversible()
    {
        return false;
    }
}
?>