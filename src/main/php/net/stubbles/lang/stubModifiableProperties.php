<?php
/**
 * Properties instance which allows modification of properties.
 *
 * @package     stubbles
 * @subpackage  lang
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::stubProperties');
/**
 * Properties instance which allows modification of properties.
 *
 * @package     stubbles
 * @subpackage  lang
 * @since       1.7.0
 */
class stubModifiableProperties extends stubProperties
{
    /**
     * construct class from a file
     *
     * @param   string                     $propertiesFile  full path to file containing properties
     * @return  stubModifiableProperties
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
     * sets a section
     *
     * If a section with this name already exists it will be replaced.
     *
     * @param   string                    $section
     * @param   array                     $data
     * @return  stubModifiableProperties
     */
    public function setSection($section, array $data)
    {
        $this->propertyData[$section] = $data;
        return $this;
    }

    /**
     * sets value of property in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @param   string                    $section
     * @param   string                    $name
     * @param   mixed                     $value
     * @return  stubModifiableProperties
     */
    public function setValue($section, $name, $value)
    {
        if (isset($this->propertyData[$section]) === false) {
            $this->propertyData[$section] = array();
        }

        $this->propertyData[$section][$name] = (string) $value;
        return $this;
    }

    /**
     * sets a boolean property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @param   string                    $section
     * @param   string                    $name
     * @param   bool                      $value
     * @return  stubModifiableProperties
     */
    public function setBooleanValue($section, $name, $value)
    {
        return $this->setValue($section, $name, ((true === $value) ? ('true') : ('false')));
    }

    /**
     * sets an array as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @param   string                    $section
     * @param   string                    $name
     * @param   array                     $value
     * @return  stubModifiableProperties
     */
    public function setArrayValue($section, $name, array $value)
    {
        return $this->setValue($section, $name, join('|', $value));
    }

    /**
     * sets a hash map as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @param   string                    $section
     * @param   string                    $name
     * @param   array                     $hash
     * @return  stubModifiableProperties
     */
    public function setHashValue($section, $name, array $hash)
    {
        $values = array();
        foreach($hash as $key => $val) {
            $values[] = $key . ':' . $val;
        }

        return $this->setArrayValue($section, $name, $values);
    }

    /**
     * sets a range as property value in given section
     *
     * If a property with this name in the given section already exists it will
     * be replaced.
     *
     * @param   string                    $section
     * @param   string                    $name
     * @param   array                     $range
     * @return  stubModifiableProperties
     */
    public function setRangeValue($section, $name, array $range)
    {
        return $this->setValue($section, $name, array_shift($range) . '..' . array_pop($range));
    }
}
?>