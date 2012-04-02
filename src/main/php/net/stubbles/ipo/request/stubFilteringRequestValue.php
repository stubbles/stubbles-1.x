<?php
/**
 * Value object for request values to filter them or retrieve them after validation.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubFilteringRequestValue.php 3120 2011-03-31 14:46:36Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueErrorCollection',
                      'net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::ipo::request::filter::stubFilterFactory'
);
/**
 * Value object for request values to filter them or retrieve them after validation.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @since       1.3.0
 */
class stubFilteringRequestValue extends stubBaseObject
{
    /**
     * request instance the value inherits from
     *
     * @var  stubRequestValueErrorCollection
     */
    protected $requestValueErrorCollection;
    /**
     * filter factory to create filters with
     *
     * @var  stubFilterFactory
     */
    protected $filterFactory;
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
     * @param  stubRequestValueErrorCollection  $requestValueErrorCollection  request instance the value inherits from
     * @param  stubFilterFactory                $filterFactory                filter factory to create filters with
     * @param  string                           $name                         name of value
     * @param  string                           $value                        original value
     */
    public function __construct(stubRequestValueErrorCollection $requestValueErrorCollection, stubFilterFactory $filterFactory, $name, $value)
    {
        $this->requestValueErrorCollection = $requestValueErrorCollection;
        $this->filterFactory               = $filterFactory;
        $this->name                        = $name;
        $this->value                       = $value;
    }

    /**
     * read as boolean value
     *
     * @param   bool  $default  optional  default value to fall back to
     * @return  bool
     * @since   1.7.0
     */
    public function asBool($default = null)
    {
        if (null === $this->value && null !== $default) {
            return $default;
        }

        return $this->withFilter($this->filterFactory->createForType('bool'));
    }

    /**
     * read as integer value
     *
     * @param   int    $min       optional  minimum allowed value
     * @param   int    $max       optional  maximum allowed value
     * @param   int    $default   optional  default value to fall back to
     * @param   bool   $required  optional  if a value is required, defaults to false
     * @return  int
     */
    public function asInt($min = null, $max = null, $default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('int')
                                      ->inRange($min, $max)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }
        
        return $this->withFilter($filter);
    }

    /**
     * read as float value
     *
     * @param   int    $min       optional  minimum allowed value
     * @param   int    $max       optional  maximum allowed value
     * @param   float  $default   optional  default value to fall back to
     * @param   bool   $required  optional  if a value is required, defaults to false
     * @param   int    $decimals  optional  number of decimals
     * @return  float
     */
    public function asFloat($min = null, $max = null, $default = null, $required = false, $decimals = null)
    {
        $filter = $this->filterFactory->createForType('float')
                                      ->setDecimals($decimals)
                                      ->inRange($min, $max)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as string value
     *
     * @param   int     $minLength  optional  minimum length of string
     * @param   int     $maxLength  optional  maximum length of string
     * @param   string  $default    optional  default value to fall back to
     * @param   bool    $required   optional  if a value is required, defaults to false
     * @return  string
     */
    public function asString($minLength = null, $maxLength = null, $default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('string')
                                      ->length($minLength, $maxLength)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as text value
     *
     * @param   int     $minLength  optional  minimum length of string
     * @param   int     $maxLength  optional  maximum length of string
     * @param   string  $default    optional  default value to fall back to
     * @param   bool    $required   optional  if a value is required, defaults to false
     * @return  string
     */
    public function asText($minLength = null, $maxLength = null, $default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('text')
                                      ->length($minLength, $maxLength)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as json value
     *
     * @param   string  $default   optional  default value to fall back to
     * @param   bool    $required  optional  if a value is required, defaults to false
     * @return  string
     */
    public function asJson($default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('json')
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as password value
     *
     * @param   int            $minDiffChars      optional  minimum amount of different characters within password
     * @param   array<string>  $nonAllowedValues  optional  list of values that are not allowed as password
     * @param   bool           $required          optional  if a value is required, defaults to false
     * @return  string
     */
    public function asPassword($minDiffChars = 5, array $nonAllowedValues = array(), $required = false)
    {
        $filter = $this->filterFactory->createForType('password')
                                      ->minDiffChars($minDiffChars)
                                      ->nonAllowedValues($nonAllowedValues);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as http url
     *
     * @param   bool                  $checkDns   optional  whether url should be checked via DNS
     * @param   stubHTTPURLContainer  $default    optional  default value to fall back to
     * @param   bool                  $required   optional  if a value is required, defaults to false
     * @return  stubHTTPURL
     */
    public function asHttpUrl($checkDns = false, stubHTTPURLContainer $default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('http')
                                      ->setCheckDNS($checkDns)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as mail address
     *
     * @param   bool    $required  optional  if a value is required, defaults to false
     * @return  string
     */
    public function asMailAddress($required = false)
    {
        $filter = $this->filterFactory->createForType('mail');
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read as date value
     *
     * @param   stubDate  $minDate    optional  smallest allowed date
     * @param   stubDate  $maxDate    optional  greatest allowed date
     * @param   stubDate  $default    optional  default value to fall back to
     * @param   bool      $required   optional  if a value is required, defaults to false
     * @return  stubDate

     */
    public function asDate(stubDate $minDate = null, stubDate $maxDate = null, stubDate $default = null, $required = false)
    {
        $filter = $this->filterFactory->createForType('date')
                                      ->inPeriod($minDate, $maxDate)
                                      ->defaultsTo($default);
        if (true === $required) {
            $filter->asRequired();
        }

        return $this->withFilter($filter);
    }

    /**
     * read a value of given type
     *
     * @param   string  $type  type of variable to read
     * @return  mixed
     */
    public function asType($type)
    {
        return $this->withFilter($this->filterFactory->createForType($type));
    }

    /**
     * filters value with given filter
     *
     * If value does not satisfy given filter return value will be null.
     *
     * @param   stubFilter  $filter
     * @return  mixed
     */
    public function withFilter(stubFilter $filter)
    {
        try {
            $value = $filter->execute($this->value);
        } catch (stubFilterException $fe) {
            $this->requestValueErrorCollection->add($fe->getError(), $this->name);
            $value = null;
        }

        return $value;
    }

    /**
     * returns value if it contains given string, and null otherwise
     *
     * @param   string  $contained  byte sequence the value must contain
     * @param   string  $default    optional  default value to fall back to
     * @return  string
     */
    public function ifContains($contained, $default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubContainsValidator');
        return $this->withValidator(new stubContainsValidator($contained), $default);
    }

    /**
     * returns value if it eqals an expected value, and null otherwise
     *
     * @param   string  $expected  byte sequence the value must be equal to
     * @param   string  $default   optional  default value to fall back to
     * @return  bool
     */
    public function ifIsEqualTo($expected, $default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubEqualValidator');
        return $this->withValidator(new stubEqualValidator($expected), $default);
    }

    /**
     * returns value if it is an http url, and null otherwise
     *
     * @param   bool    $checkDns  optional  whether to verify url via DNS
     * @param   string  $default   optional  default value to fall back to
     * @return  string
     */
    public function ifIsHttpUrl($checkDns = false, $default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubHTTPURLValidator');
        return $this->withValidator(new stubHTTPURLValidator($checkDns), $default);
    }

    /**
     * returns value if it is an ip address, and null otherwise
     *
     * @param   string  $default  optional  default value to fall back to
     * @return  string
     */
    public function ifIsIpAddress($default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubIpValidator');
        return $this->withValidator(new stubIpValidator(), $default);
    }

    /**
     * returns value if it is a mail address, and null otherwise
     *
     * @param   string  $default  optional  default value to fall back to
     * @return  string
     */
    public function ifIsMailAddress($default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubMailValidator');
        return $this->withValidator(new stubMailValidator(), $default);
    }

    /**
     * returns value if it is an allowed value according to list of allowed values, and null otherwise
     *
     * @param   array<string>  $allowedValues  list of allowed values
     * @param   string         $default        optional  default value to fall back to
     * @return  string
     */
    public function ifIsOneOf(array $allowedValues, $default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubPreSelectValidator');
        return $this->withValidator(new stubPreselectValidator($allowedValues), $default);
    }

    /**
     * returns value if it complies to a given regular expression, and null otherwise
     *
     * @param   string  $regex    regular expression to apply
     * @param   string  $default  optional  default value to fall back to
     * @return  string
     */
    public function ifSatisfiesRegex($regex, $default = null)
    {
        stubClassLoader::load('net::stubbles::ipo::request::validator::stubRegexValidator');
        return $this->withValidator(new stubRegexValidator($regex), $default);
    }

    /**
     * checks value with given validator
     *
     * If value does not satisfy the validator return value will be null.
     *
     * @param   stubValidator  $validator  validator to use
     * @param   string         $default    optional  default value to fall back to
     * @return  string
     */
    public function withValidator(stubValidator $validator, $default = null)
    {
        if ($validator->validate($this->value) === true) {
            return $this->value;
        }

        return $default;
    }

    /**
     * returns value unvalidated
     *
     * This should be used with greatest care.
     *
     * @return  string
     */
    public function unsecure()
    {
        return $this->value;
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