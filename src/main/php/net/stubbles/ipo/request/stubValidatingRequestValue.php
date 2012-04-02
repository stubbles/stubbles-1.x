<?php
/**
 * Value object for request values to check them against validators.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubValidatingRequestValue.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubFilter');
/**
 * Value object for request values to check them against validators.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
class stubValidatingRequestValue extends stubBaseObject
{
    /**
     * original value
     *
     * @var  string
     */
    protected $value;
    /**
     * name of value
     *
     * @var  string
     */
    protected $name;

    /**
     * constructor
     *
     * @param  string  $name   name of value
     * @param  string  $value  original value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * checks whether value contains given string
     *
     * @param   string  $contained  byte sequence the value must contain
     * @return  bool
     */
    public function contains($contained)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubContainsValidator');
        return $this->withValidator(new stubContainsValidator($contained));
    }


    /**
     * checks whether value equals given string
     *
     * @param   string  $expected   byte sequence the value must be equal to
     * @return  bool
     */
    public function isEqualTo($expected)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubEqualValidator');
        return $this->withValidator(new stubEqualValidator($expected));
    }

    /**
     * checks whether value is an http url
     *
     * @param   bool    $checkDns  optional  whether to verify url via DNS
     * @return  bool
     */
    public function isHttpUrl($checkDns = false)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubHTTPURLValidator');
        return $this->withValidator(new stubHTTPURLValidator($checkDns));
    }

    /**
     * checks whether value is an ip address, where both IPv4 and IPv6 are valid
     *
     * @return  bool
     */
    public function isIpAddress()
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubIpValidator');
        return $this->withValidator(new stubIpValidator());
    }

    /**
     * checks whether value is an ip v4 address
     *
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV4Address()
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubIpV4Validator');
        return $this->withValidator(new stubIpV4Validator());
    }

    /**
     * checks whether value is an ip v6 address
     *
     * @return  bool
     * @since   1.7.0
     */
    public function isIpV6Address()
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubIpV6Validator');
        return $this->withValidator(new stubIpV6Validator());
    }

    /**
     * checks whether value is a mail address
     *
     * @return  string
     */
    public function isMailAddress()
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubMailValidator');
        return $this->withValidator(new stubMailValidator());
    }

    /**
     * checks whether value is in a list of allowed values
     *
     * @param   array<string>  $allowedValues  list of allowed values
     * @return  bool
     */
    public function isOneOf(array $allowedValues)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubPreSelectValidator');
        return $this->withValidator(new stubPreselectValidator($allowedValues));
    }

    /**
     * checks whether value satisfies given regular expression
     *
     * @param   string  $regex  regular expression to apply
     * @return  bool
     */
    public function satisfiesRegex($regex)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubRegexValidator');
        return $this->withValidator(new stubRegexValidator($regex));
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @param   stubValidator  $validator  validator to use
     * @return  string
     */
    public function withValidator(stubValidator $validator)
    {
        return $validator->validate($this->value);
    }

    /**
     * returns name of value
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }
}
?>