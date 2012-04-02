<?php
/**
 * Class for console output streams.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubConsoleOutputStream.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubResourceOutputStream');
/**
 * Class for console output streams.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubConsoleOutputStream extends stubResourceOutputStream
{
    /**
     * holds output stream instance if created
     *
     * @var  stubConsoleOutputStream
     */
    protected static $out;
    /**
     * holds error stream instance if created
     *
     * @var  stubConsoleOutputStream
     */
    protected static $err;

    /**
     * constructor
     *
     * @param  resource  $descriptor
     */
    protected function __construct($descriptor)
    {
        $this->setHandle($descriptor);
    }

    /**
     * comfort method for getting a console output stream
     *
     * @return  stubConsoleOutputStream
     */
    public static function forOut()
    {
        if (null === self::$out) {
            self::$out      = new self(STDOUT);
            $outputEncoding = self::getOutputEncoding();
            if ('UTF-8' !== $outputEncoding) {
                stubClassLoader::load('net::stubbles::streams::stubEncodingOutputStream');
                self::$out = new stubEncodingOutputStream(self::$out, $outputEncoding . '//IGNORE');
            }
        }
        
        return self::$out;
    }

    /**
     * comfort method for getting a console error stream
     *
     * @return  stubConsoleOutputStream
     */
    public static function forError()
    {
        if (null === self::$err) {
            self::$err      = new self(STDERR);
            $outputEncoding = self::getOutputEncoding();
            if ('UTF-8' !== $outputEncoding) {
                stubClassLoader::load('net::stubbles::streams::stubEncodingOutputStream');
                self::$err = new stubEncodingOutputStream(self::$err, $outputEncoding . '//IGNORE');
            }
        }
        
        return self::$err;
    }

    /**
     * helper method to detect correct output encoding
     *
     * @return  string
     */
    protected static function getOutputEncoding()
    {
        $outputEncoding = iconv_get_encoding('output_encoding');
        if ('CP1252' === $outputEncoding && DIRECTORY_SEPARATOR !== '/') {
            $outputEncoding = 'CP850';
        }
        
        return $outputEncoding;
    }
}
?>