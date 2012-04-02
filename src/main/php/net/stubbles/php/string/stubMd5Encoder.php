<?php
/**
 * Encoder for md5.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubMd5Encoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::php::string::stubAbstractStringEncoder');
/**
 * Encoder for md5.
 *
 * @package     stubbles
 * @subpackage  php_string
 */
class stubMd5Encoder extends stubAbstractStringEncoder
{
    /**
     * prefix of the value to filter
     *
     * @var  string
     */
    protected $prefix = '';
    /**
     * postfix of the value to filter
     *
     * @var  string
     */
    protected $postfix = '';

    /**
     * constructor
     *
     * @param  string  $prefix   prefix of the value to filter
     * @param  string  $postfix  postfix of the value to filter
     */
    public function __construct($prefix = '', $postfix = '')
    {
        $this->setPrefix($prefix);
        $this->setPostfix($postfix);
    }

    /**
     * sets the prefix
     *
     * @param  string  $prefix  prefix of the value to filter
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * sets the postfix
     *
     * @param  string  $postfix  postfix of the value to filter
     */
    public function setPostfix($postfix)
    {
        $this->postfix = $postfix;
    }

    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        return md5($this->prefix . $string . $this->postfix);
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
        throw new stubMethodNotSupportedException('Can not decode md5-encoded string ' . $string . ', encoding is not reversible.');
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