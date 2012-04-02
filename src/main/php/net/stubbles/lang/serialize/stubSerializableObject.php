<?php
/**
 * base class for all other stubbles classes except static ones and classes
 * extending php built-in classes
 * 
 * @package     stubbles
 * @subpackage  lang_serialize
 * @version     $Id: stubSerializableObject.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * base class for all other stubbles classes except static ones and classes
 * extending php built-in classes
 * 
 * @package     stubbles
 * @subpackage  lang_serialize
 */
class stubSerializableObject extends stubBaseObject implements stubSerializable
{
    /**
     * a list of serialized properties
     * 
     * Do not ever use this property in extended classes. It is only protected
     * and not private because of some PHP-$�&%&�%&!
     *
     * @var  array<string,stubSerializedObject>
     */
    protected $_serializedProperties = array();

    /**
     * returns a serialized representation of the class
     * 
     * @return  stubSerializedObject
     * @XMLIgnore
     */
    public function getSerialized()
    {
        $serialized = new stubSerializedObject($this);
        return $serialized;
    }

    /**
     * ensure that all instances of stubSerializable are correctly serialized
     *
     * @return  array<string>  list of properties to serialize
     * @XMLIgnore
     */
    public final function __sleep()
    {
        $this->_serializedProperties = array();
        $nonAllowedProperties        = $this->__doSleep();
        $propertiesToSerialize       = array();
        foreach ($this->_extractProperties($this) as $name => $value) {
            if (in_array($name, $nonAllowedProperties) == true) {
                continue;
            }
            
            $this->__doSerialize($propertiesToSerialize, $name, $value);
        }
        
        return $propertiesToSerialize;
    }

    /**
     * template method to hook into __sleep()
     *
     * @return  array<string>  list of property names that should not be serialized
     */
    protected function __doSleep()
    {
        return array();
    }

    /**
     * takes care of serializing the value
     *
     * @param  array   &$propertiesToSerialize  list of properties to serialize
     * @param  string  $name                    name of the property to serialize
     * @param  mixed   $value                   value to serialize
     */
    protected function __doSerialize(&$propertiesToSerialize, $name, $value)
    {
        if ($value instanceof stubSerializable) {
            $this->_serializedProperties[$name] = $value->getSerialized();
        } else {
            $propertiesToSerialize[] = $name;
        }
    }
    
    /**
     * restore all instances that are of type stubSerializable
     * 
     * @XMLIgnore
     */
    public final function __wakeup()
    {
        $this->__doWakeUp();
        foreach ($this->_serializedProperties as $name => $serializedValue) {
            $this->__doUnserialize($name, $serializedValue);
        }
        
        $this->_serializedProperties = array();
    }

    /**
     * template method to hook into __wakeup()
     */
    protected function __doWakeUp()
    {
        // intentionally empty
    }

    /**
     * takes care of unserializing the value
     *
     * @param  string  $name             name of the property
     * @param  mixed   $serializedValue  value of the property
     */
    protected function __doUnserialize($name, $serializedValue)
    {
        if ($serializedValue instanceof stubSerializedObject) {
            $this->$name = $serializedValue->getUnserialized();
        } else {
            $this->$name = $serializedValue;
        }
    }
}
?>