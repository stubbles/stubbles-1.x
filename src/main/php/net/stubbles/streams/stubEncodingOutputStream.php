<?php
/**
 * Encodes internal encoding into output charset.
 *
 * @package     stubbles
 * @subpackage  streams
 * @version     $Id: stubEncodingOutputStream.php 2292 2009-08-20 20:23:56Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedOutputStream');
/**
 * Encodes internal encoding into output charset.
 *
 * @package     stubbles
 * @subpackage  streams
 */
class stubEncodingOutputStream extends stubAbstractDecoratedOutputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @var  stubOutputStream
     */
    protected $outputStream;
    /**
     * charset of output stream
     *
     * @var  string
     */
    protected $charset;

    /**
     * constructor
     *
     * @param  stubOutputStream  $outputStream
     * @param  string            $charset       charset of output stream
     */
    public function __construct(stubOutputStream $outputStream, $charset)
    {
        $this->outputStream = $outputStream;
        $this->charset      = $charset;
    }

    /**
     * returns charset of output stream
     *
     * @return  string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->outputStream->write(iconv('UTF-8', $this->charset, $bytes));
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->outputStream->writeLine(iconv('UTF-8', $this->charset, $bytes));
    }
}
?>