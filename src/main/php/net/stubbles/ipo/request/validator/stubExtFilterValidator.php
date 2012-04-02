<?php
/**
 * Validator to wrap the validators provided by ext/filter
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubExtFilterValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Validator to wrap the validators provided by ext/filter
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @link        http://www.php.net/filter
 */
class stubExtFilterValidator extends stubBaseObject implements stubValidator
{
    /**
     * The filter to use
     *
     * @var  int
     */
    protected $filter;

    /**
     * The options to use
     *
     * @var  array
     */
    protected $options = array();

    /**
     * The flags to use
     *
     * @var  int
     */
    protected $flags = FILTER_FLAG_NONE;

    /**
     * constructor
     *
     * @param  string  $filter   name of filter to apply
     * @param  array   $options  optional  options for the filter
     * @param  int     $flags    optional  flags for the filter
     */
    public function __construct($filter, array $options = array(), $flags = FILTER_FLAG_NONE)
    {
        $this->filter  = $filter;
        $this->options = $options;
        $this->flags   = $flags;
    }

    /**
     * validate that the given value is greater than or equal to the maximum value
     *
     * @param   int|double  $value
     * @return  bool        true if value is greater than or equal to minimum value, else false
     */
    public function validate($value)
    {
        $result = filter_var($value, $this->filter, array('options' => $this->options, 'flags' => $this->flags));
        if ($result === false) {
            return false;
        }
        
        return true;
    }

    /**
     * returns a list of criteria for the validator
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array();
    }
}
?>