<?php
/**
 * Encoder/decoder for special HTML characters.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubHTMLSpecialCharsEncoder.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::string::stubAbstractStringEncoder'
);
/**
 * Encoder/decoder for special HTML characters.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @see         http://php.net/htmlspecialchars
 */
class stubHTMLSpecialCharsEncoder extends stubAbstractStringEncoder
{
    /**
     * style for how to encode quotes
     *
     * @var  int
     */
    protected $quoteStyle   = ENT_QUOTES;
    /**
     * the charset to use for the encoding
     *
     * @var  string
     */
    protected $charset      = 'UTF-8';
    /**
     * switch whether to encode already encoded parts again
     *
     * @var  bool
     */
    protected $doubleEncode = false;

    /**
     * sets the quote style to use
     *
     * @param   int  $quoteStyle
     * @throws  stubIllegalArgumentException
     */
    public function setQuoteStyle($quoteStyle)
    {
        if (in_array($quoteStyle, array(ENT_QUOTES, ENT_NOQUOTES, ENT_COMPAT)) === false) {
            throw new stubIllegalArgumentException('Quote style must be one of ENT_QUOTES, ENT_NOQUOTES or ENT_COMPAT.');
        }
        
        $this->quoteStyle = $quoteStyle;
    }

    /**
     * character set used for encoding
     * 
     * See http://php.net/htmlspecialchars for a list of supported character
     * sets. Unrecognized charsets will fall back to ISO-8859-1.
     *
     * @param  string  $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * sets whether already encoded parts should be encoded again or not
     *
     * @param  bool  $doubleEncode
     */
    public function setDoubleEncode($doubleEncode)
    {
        $this->doubleEncode = (bool) $doubleEncode;
    }

    /**
     * encodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function encode($string)
    {
        if (version_compare(PHP_VERSION, '5.2.3') === -1) {
            return htmlspecialchars($string, $this->quoteStyle, $this->charset);
        }
        
        return htmlspecialchars($string, $this->quoteStyle, $this->charset, $this->doubleEncode);
    }

    /**
     * decodes a string
     *
     * @param   string  $string
     * @return  string
     */
    public function decode($string)
    {
        return htmlspecialchars_decode($string, $this->quoteStyle);
    }
}
?>