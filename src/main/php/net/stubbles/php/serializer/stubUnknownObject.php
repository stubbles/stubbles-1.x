<?php
/**
 * Represents an unserialized object where the appropriate class was not loaded.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubUnknownObject.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalAccessException');
/**
 * Represents an unserialized object where the appropriate class was not loaded.
 * 
 * Taken from the XP frameworks's class remote.UnknownRemoteObject.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubUnknownObject extends stubBaseObject
{
    /**
     * name of the unknown class
     *
     * @var  string
     */
    protected $name;
    /**
     * properties of the unknown class
     *
     * @var  array<string,mixed>
     */
    protected $properties;

    /**
     * constructor
     *
     * @param  string               $name        name of the unknown class
     * @param  array<string,mixed>  $properties  properties of the unknown class
     */
    public function __construct($name, $properties)
    {
        $this->name       = $name;
        $this->properties = $properties;
    }

    /**
     * returns the name of the unknown class
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * returns the properties of the unknown class
     *
     * @return  array<string,mixed>
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * member set interceptor
     *
     * @param   string  $name
     * @param   mixed   $value
     * @throws  stubIllegalAccessException
     */
    public function __set($name, $value)
    {
        throw new stubIllegalAccessException('Access to undefined member "' . $name . '"');
    }

    /**
     * member get interceptor
     *
     * @param   string  $name
     * @throws  stubIllegalAccessException
     */
    public function __get($name)
    {
        throw new stubIllegalAccessException('Access to undefined member "' . $name . '"');
    }

    /**
     * method call interceptor
     *
     * @param   string  $name
     * @param   array   $args
     * @throws  stubIllegalAccessException
     */
    public function __call($name, $args)
    {
        throw new stubIllegalAccessException('Cannot call method "' . $name . '" on an unknown object');
    }

    /**
     * clone interceptor
     *
     * @throws  stubIllegalAccessException
     */
    public function __clone()
    {
        throw new stubIllegalAccessException('Cannot clone an unknown object');
    }
}
?>