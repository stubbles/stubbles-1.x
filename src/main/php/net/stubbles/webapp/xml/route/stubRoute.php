<?php
/**
 * Container for route resources.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 * @version     $Id: stubRoute.php 3183 2011-09-05 09:59:31Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubProperties',
                      'net::stubbles::webapp::xml::route::stubProcessable'
);
/**
 * Container for route resources.
 *
 * A route can have two elements:
 * - Properties are the simplest ones, this are scalar values which may be used
 *   for some configuration options.
 * - Processables are a list of implementations of net::stubbles::webapp::xml::route::Processable
 *   and should be processed by the processor.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_route
 */
class stubRoute extends stubBaseObject
{
    /**
     * property data
     *
     * @var  stubProperties
     */
    protected $properties;

    /**
     * constructor
     *
     * @param  stubProperties  $properties
     */
    public function  __construct(stubProperties $properties)
    {
        $this->properties = $properties;
    }

    /**
     * checks whether a property for the route exists
     *
     * @param   string  $name  name of the property
     * @return  bool
     */
    public function hasProperty($name)
    {
        return $this->properties->hasValue('properties', $name);
    }

    /**
     * returns the property or null if it does not exist
     *
     * @param   string  $name     name of the property
     * @param   scalar  $default  optional  default value to return if property not set
     * @return  scalar
     */
    public function getProperty($name, $default = null)
    {
        return $this->properties->getValue('properties', $name, $default);
    }

    /**
     * returns property as integer or 0 if it does not exist
     *
     * @param   string  $name
     * @param   int     $default  optional
     * @return  int
     */
    public function getPropertyAsInt($name, $default = 0)
    {
        return $this->properties->parseInt('properties', $name, $default);
    }

    /**
     * returns property as float or 0.0 if it does not exist
     *
     * @param   string  $name
     * @param   float   $default  optional
     * @return  float
     */
    public function getPropertyAsFloat($name, $default = 0.0)
    {
        return $this->properties->parseFloat('properties', $name, $default);
    }

    /**
     * returns property as bool or false if it does not exist
     *
     * @param   string  $name
     * @param   bool    $default  optional
     * @return  bool
     */
    public function getPropertyAsBool($name, $default = false)
    {
        return $this->properties->parseBool('properties', $name, $default);
    }

    /**
     * returns property as array or null if it does not exist
     *
     * @param   string  $name
     * @param   array   $default  optional
     * @return  array
     */
    public function getPropertyAsArray($name, array $default = null)
    {
        return $this->properties->parseArray('properties', $name, $default);
    }

    /**
     * returns property as hashmap or null if it does not exist
     *
     * @param   string  $name
     * @param   array   $default  optional
     * @return  array
     */
    public function getPropertyAsHash($name, array $default = null)
    {
        return $this->properties->parseHash('properties', $name, $default);
    }

    /**
     * returns property as range or null if it does not exist
     *
     * @param   string  $name
     * @param   array   $default  optional
     * @return  array
     */
    public function getPropertyAsRange($name, array $default = array())
    {
        return $this->properties->parseRange('properties', $name, $default);
    }

    /**
     * returns the list of processables
     *
     * @return  array<string,stubProcessable>
     */
    public function getProcessables()
    {
        return $this->properties->getSection('processables', array());
    }
}
?>