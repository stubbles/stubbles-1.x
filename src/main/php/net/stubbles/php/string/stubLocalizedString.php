<?php
/**
 * Class containing a localized string.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @version     $Id: stubLocalizedString.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Class containing a localized string.
 *
 * @package     stubbles
 * @subpackage  php_string
 * @XMLTag(tagName='string')
 */
class stubLocalizedString extends stubBaseObject
{
    /**
     * locale of the message
     *
     * @var  string
     */
    protected $locale;
    /**
     * content of the message
     *
     * @var  string
     */
    protected $message;

    /**
     * constructor
     *
     * @param  string  $locale
     * @param  string  $message
     */
    public function __construct($locale, $message)
    {
        $this->locale  = $locale;
        $this->message = $message;
    }

    /**
     * returns the locale of the message
     *
     * @return  string
     * @XMLAttribute(attributeName='locale')
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * returns the content of the message
     *
     * @return  string
     * @XMLTag(tagName='content')
     */
    public function getMessage()
    {
        return $this->message;
    }
}
?>