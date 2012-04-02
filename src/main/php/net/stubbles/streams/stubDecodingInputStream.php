<?php
/**
 * Decodes input stream into internal charset.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubDecodingInputStream.php 2292 2009-08-20 20:23:56Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedInputStream');
/**
 * Decodes input stream into internal charset.
 *
 * @package     stubbles
 * @subpackage  streams
 */
class stubDecodingInputStream extends stubAbstractDecoratedInputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @var  stubInputStream
     */
    protected $inputStream;
    /**
     * charset of input stream
     *
     * @var  string
     */
    protected $charset;

    /**
     * constructor
     *
     * @param  stubInputStream  $inputStream
     * @param  string           $charset      charset of input stream
     */
    public function __construct(stubInputStream $inputStream, $charset)
    {
        $this->inputStream = $inputStream;
        $this->charset     = $charset;
    }

    /**
     * returns charset of input stream
     *
     * @return  string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->read($length));
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int     $length  optional  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->readLine($length));
    }
}
?>