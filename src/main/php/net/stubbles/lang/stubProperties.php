<?php
/**
 * Class to read and parse properties.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id: stubProperties.php 2793 2010-11-25 22:40:19Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubFileNotFoundException',
                      'net::stubbles::lang::exceptions::stubIOException'
);
/**
 * Class to read and parse properties.
 *
 * Properties are iterable using foreach:
 * <code>
 * foreach (stubProperties::fromFile($propertyFile) as $sectionName => $section) {
 *     // $section is an array containing all section values as key-value pairs
 * }
 * </code>
 *
 * @package     stubbles
 * @subpackage  lang
 */
class stubProperties extends stubBaseObject implements Iterator
{
    /**
     * property data
     *
     * @var  array
     */
    protected $propertyData;

    /**
     * constructor
     *
     * @param  array  $propertyData  optional  the property data
     */
    public function __construct(array $propertyData = array())
    {
        $this->propertyData = $propertyData;
    }

    /**
     * construct class from a file
     *
     * Note: the stubIOException may only be thrown with PHP versions >= 5.2.7
     * because starting with this version PHP's parse_ini_file() returns false
     * if it can not parse the property file, before this version it just returns
     * an array which we can not distinguish from an empty property file.
     *
     * @param   string                     $propertiesFile  full path to file containing properties
     * @return  stubProperties
     * @throws  stubFileNotFoundException  if file can not be found or is not readable
     * @throws  stubIOException            if file contains errors and can not be parsed
     */
    public static function fromFile($propertiesFile)
    {
        if (file_exists($propertiesFile) === false || is_readable($propertiesFile) === false) {
            throw new stubFileNotFoundException($propertiesFile);
        }
        
        $propertyData = @parse_ini_file($propertiesFile, true);
        if (false === $propertyData) {
            throw new stubIOException('Property file at ' . $propertiesFile . ' contains errors and can not be parsed.');
        }
        
        return new self($propertyData);
    }

    /**
     * merges properties from another instance into itself
     *
     * The return value is a new instance with properties from this and the other
     * instance combined. If both instances have sections of the same name the
     * section from the other instance overwrite the section from this instance.
     *
     * @param   stubProperties  $otherProperties
     * @return  stubProperties
     * @since   1.3.0
     */
    public function merge(self $otherProperties)
    {
        return new self(array_merge($this->propertyData, $otherProperties->propertyData));
    }

    /**
     * returns a list of section keys
     *
     * @return  array<string>
     */
    public function getSections()
    {
        return array_keys($this->propertyData);
    }

    /**
     * checks if a certain section exists
     *
     * @param   string  $section  name of the section
     * @return  bool
     */
    public function hasSection($section)
    {
        return isset($this->propertyData[$section]);
    }

    /**
     * returns a whole section if it exists or the default if the section does not exist
     *
     * @param   string                $section  name of the section
     * @param   array                 $default  optional  value to return if section does not exist
     * @return  array<string,scalar>
     */
    public function getSection($section, array $default = array())
    {
        if (isset($this->propertyData[$section]) === true) {
            return $this->propertyData[$section];
        }
        
        return $default;
    }

    /**
     * returns a list of all keys of a specific section
     *
     * @param   string         $section  name of the section
     * @param   array<string>  $default  optional  value to return if section does not exist
     * @return  array<string>
     */
    public function getSectionKeys($section, array $default = array())
    {
        if (isset($this->propertyData[$section]) === true) {
            return array_keys($this->propertyData[$section]);
        }

        return $default;
    }

    /**
     * checks if a certain section contains a certain key
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @return  bool
     */
    public function hasValue($section, $key)
    {
        if (isset($this->propertyData[$section]) === true && isset($this->propertyData[$section][$key]) === true) {
            return true;
        }
        
        return false;
    }

    /**
     * returns a value from a section or a default value if the section or key does not exist
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   mixed   $default  optional  value to return if section or key does not exist
     * @return  scalar
     */
    public function getValue($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) === true && isset($this->propertyData[$section][$key]) === true) {
            return $this->propertyData[$section][$key];
        }
        
        return $default;
    }

    /**
     * returns a string from a section or a default string if the section or key does not exist
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   string  $default  optional  string to return if section or key does not exist
     * @return  string
     */
    public function parseString($section, $key, $default = null)
    {
        if (isset($this->propertyData[$section]) === true && isset($this->propertyData[$section][$key]) === true) {
            return (string) $this->propertyData[$section][$key];
        }
        
        return $default;
    }

    /**
     * returns an integer or a default value if the section or key does not exist
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   int     $default  optional  value to return if section or key does not exist
     * @return  int
     */
    public function parseInt($section, $key, $default = 0)
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        return intval($this->propertyData[$section][$key]);
    }

    /**
     * returns a float or a default value if the section or key does not exist
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   float   $default  optional  value to return if section or key does not exist
     * @return  float
     */
    public function parseFloat($section, $key, $default = 0.0)
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        return floatval($this->propertyData[$section][$key]);
    }

    /**
     * returns a boolean or a default value if the section or key does not exist
     *
     * The return value is true if the property value is either "1", "yes",
     * "true" or "on". In any other case the return value will be false.
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   bool    $default  optional  value to return if section or key does not exist
     * @return  bool
     */
    public function parseBool($section, $key, $default = false)
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        $val = $this->propertyData[$section][$key];
        return ('1' == $val || 'yes' === $val || 'true' === $val || 'on' === $val);
    }

    /**
     * returns an array from a section or a default array if the section or key does not exist
     *
     * If the value is empty the return value will be an empty array. If the
     * value is not empty it will be splitted at "|".
     * Example:
     * <code>
     * key = "foo|bar|baz"
     * </code>
     * The resulting array would be array('foo', 'bar', 'baz')
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  optional  array to return if section or key does not exist
     * @return  array
     */
    public function parseArray($section, $key, array $default = null)
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        if (empty($this->propertyData[$section][$key]) === true) {
            return array();
        }
        
        return explode('|', $this->propertyData[$section][$key]);
    }

    /**
     * returns a hash from a section or a default hash if the section or key does not exist
     *
     * If the value is empty the return value will be an empty hash. If the
     * value is not empty it will be splitted at "|". The resulting array will
     * be splitted at the first ":", the first part becoming the key and the rest
     * becoming the value in the hash. If no ":" is present, the whole value will
     * be appended to the hash using a numeric value.
     * Example:
     * <code>
     * key = "foo:bar|baz"
     * </code>
     * The resulting hash would be array('foo' => 'bar', 'baz')
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  optional  array to return if section or key does not exist
     * @return  array
     */
    public function parseHash($section, $key, array $default = null)
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        if (empty($this->propertyData[$section][$key]) === true) {
            return array();
        }
        
        $hash = array();
        foreach (explode('|', $this->propertyData[$section][$key]) as $keyValue) {
            if (strstr($keyValue, ':') !== false) {
                list($key, $value) = explode(':', $keyValue, 2);
                $hash[$key]        = $value;
            } else {
                $hash[] = $keyValue;
            }
        }
        
        return $hash;
    }

    /**
     * returns an array containing values from min to max of the range or a default if the section or key does not exist
     *
     * Ranges in properties should be written as
     * <code>
     * key = 1..5
     * </code>
     * This will return an array: array(1, 2, 3, 4, 5)
     * Works also with letters and reverse order:
     * <code>
     * letters = a..e
     * letter_reverse = e..a
     * numbers_reverese = 1..5
     * </code>
     *
     * @param   string  $section  name of the section
     * @param   string  $key      name of the key
     * @param   array   $default  optional  range to return if section or key does not exist
     * @return  array
     */
    public function parseRange($section, $key, array $default = array())
    {
        if (isset($this->propertyData[$section]) === false || isset($this->propertyData[$section][$key]) === false) {
            return $default;
        }
        
        if (strstr($this->propertyData[$section][$key], '..') === false) {
            return array();
        }
        
        list($min, $max) = explode('..', $this->propertyData[$section][$key]);
        if (null == $min || null == $max) {
            return array();
        }
        
        return range($min, $max);
    }

    /**
     * returns current section
     *
     * @return  array
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function current()
    {
        return current($this->propertyData);
    }

    /**
     * returns name of current section
     *
     * @return  string
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function key()
    {
        return key($this->propertyData);
    }

    /**
     * forwards to next section
     *
     * @see  http://php.net/manual/en/spl.iterators.php
     */
    public function next()
    {
        next($this->propertyData);
    }

    /**
     * rewind to first section
     *
     * @see  http://php.net/manual/en/spl.iterators.php
     */
    public function rewind()
    {
        reset($this->propertyData);
    }

    /**
     * checks if there are more valid sections
     *
     * @return  bool
     * @see     http://php.net/manual/en/spl.iterators.php
     */
    public function valid()
    {
        return current($this->propertyData);
    }
}
?>