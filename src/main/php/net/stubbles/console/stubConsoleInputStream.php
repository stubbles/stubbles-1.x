<?php
/**
 * Class for console input streams.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubConsoleInputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubResourceInputStream');
/**
 * Class for console input streams.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubConsoleInputStream extends stubResourceInputStream
{
    /**
     * holds input stream instance if created
     *
     * @var  stubConsoleInputStream
     */
    protected static $in;

    /**
     * constructor
     */
    protected function __construct()
    {
        $this->setHandle(STDIN);
    }

    /**
     * comfort method for getting a console output stream
     *
     * @return  stubConsoleInputStream
     */
    public static function forIn()
    {
        if (null === self::$in) {
            self::$in      = new self();
            $inputEncoding = iconv_get_encoding('input_encoding');
            if ('UTF-8' !== $inputEncoding) {
                stubClassLoader::load('net::stubbles::streams::stubDecodingInputStream');
                self::$in = new stubDecodingInputStream(self::$in, $inputEncoding);
            }
        }
        
        return self::$in;
    }
}
?>