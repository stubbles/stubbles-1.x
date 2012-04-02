<?php
/**
 * Validator to ensure that a string is a http url.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubHTTPURLValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator',
                      'net::stubbles::peer::stubMalformedURLException',
                      'net::stubbles::peer::http::stubHTTPURL'
);
/**
 * Validator to ensure that a string is a http url.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubHTTPURLValidator extends stubBaseObject implements stubValidator
{
    /**
     * whether to check dns for existence of given url or not
     *
     * @var  bool
     */
    protected $checkDNS = false;

    /**
     * constructor
     *
     * @param  bool  $checkDNS  optional
     */
    public function __construct($checkDNS = false)
    {
        $this->checkDNS = $checkDNS;
    }

    /**
     * validate that the given value is a http url
     *
     * @param   string  $value
     * @return  bool
     */
    public function validate($value)
    {
        if (null == $value || strlen($value) == 0) {
            return false;
        }
        
        try {
            $url = stubHTTPURL::fromString($value);
            if (true === $this->checkDNS) {
                return $url->checkDNS();
            }
        } catch (stubMalformedURLException $murle) {
            return false;
        }
        
        return true;
    }
    
    /**
     * returns a list of criteria for the validator
     * 
     * <code>
     * array();
     * </code>
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array();
    }
}
?>