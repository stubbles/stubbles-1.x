<?php
/**
 * Class for default serialization of objects.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubPHPSerializerObjectMapping.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::serializer::stubPHPSerializerMapping'
);
/**
 * Class for default serialization of objects.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPHPSerializerObjectMapping extends stubBaseObject implements stubPHPSerializerMapping
{
    /**
     * the class to handle with this mapping
     *
     * @var  ReflectionClass
     */
    protected static $handledClass = null;

    /**
     * returns the token to be used by this mapping
     *
     * @return  string
     */
    public function getToken()
    {
        return 'O';
    }

    /**
     * return reflection instance of mapped class
     *
     * @return  ReflectionClass
     */
    public function getHandledClass()
    {
        if (null === self::$handledClass) {
            self::$handledClass = new ReflectionClass('stdClass');
        }
        
        return self::$handledClass;
    }

    /**
     * Returns an on-the-wire representation of the given object
     *
     * @param   stubPHPSerializer    $serializer  the serializer instance
     * @param   object               $object      the object to serialize
     * @param   array<string,mixed>  $context     optional  context data
     * @return  string
     * @throws  stubIllegalArgumentException
     */
    public function serialize(stubPHPSerializer $serializer, $object, array $context = array())
    {
        if (is_object($object) === false) {
            throw new stubIllegalArgumentException('Can only serialize objects.');
        }
        
        $className  = get_class($object);
        // casting the object to an array gives us access to protected
        // and private properties as well
        $properties = (array) $object;
        if (method_exists($object, '__sleep') == true) {
            $propsToSerialize = $object->__sleep();
        } else {
            $propsToSerialize = array_keys($properties);
        }
        
        $s = 'O:' . strlen($className) . ':"' . $className . '":' . sizeof($propsToSerialize) . ':{';
        foreach (array_keys($properties) as $propertyName) {
            if (in_array($propertyName, $propsToSerialize) == false
                && in_array($this->removeAccessInfoFromPropertyName($propertyName), $propsToSerialize) == false) {
                continue;
            }
            
            $s .= serialize($propertyName) . $serializer->serialize($properties[$propertyName], $context);
        }
        
        return $s . '}';
    }

    /**
     * helper method to remove access information from the property name string
     *
     * @param   string  $propertyName
     * @return  string
     */
    protected function removeAccessInfoFromPropertyName($propertyName)
    {
        if ("\0" !== $propertyName{0}) {
            return $propertyName;
        }
        
        return substr($propertyName, strrpos($propertyName, "\0") + 1);
    }

    /**
     * returns a value for the given serialized string
     *
     * @param   stubPHPSerializer      $serializer  the serializer instance
     * @param   stubPHPSerializedData  $serialized  the serialized data
     * @param   array<string,mixed>    $context     optional  context data
     * @return  mixed
     * @throws  stubFormatException
     */
    public function unserialize(stubPHPSerializer $serializer, stubPHPSerializedData $serialized, array $context = array())
    {
        $serialized->moveOffset(2); // token
        $className = $serialized->consumeString();
        $size      = $serialized->consumeSize();
        $serialized->moveOffset();  // opening "{"
        if (class_exists($className, false) == false) {
            $instance = new stubUnknownObject($className, $this->unserializeProperties($serializer, $serialized, $context, $size));
        } else {
            $refClass = new ReflectionClass($className);
            if ($refClass->hasMethod('__set_state') == false) {
                // throw away property data, just move the pointer to the end
                $this->unserializeProperties($serializer, $serialized, $context, $size);
                // use php's native unserialize -> won't work if the object
                // contains properties which have been serialized with another
                // mapping
                $instance = @unserialize($serialized->getSubData($start, $serialized->getOffset() + 1));
                if (false === $instance) {
                    throw new stubFormatException('Cannot unserialize type "' . $className . '" (' . $serialized . '), contains invalid serialized data.');
                }
            } else {
                $instance = call_user_func_array(array($className, '__set_state'), array($this->unserializeProperties($serializer, $serialized, $context, $size, true)));
            }
            
            if (method_exists($instance, '__wakeup') == true) {
                $instance->__wakeup();
            }
        }
        
        $serialized->moveOffset(); // closing "}"
        return $instance;
    }

    /**
     * strips all members out of the serialized data
     *
     * @param   stubPHPSerializer      $serializer    the serializer instance
     * @param   stubPHPSerializedData  $serialized    the serialized data
     * @param   array<string,mixed>    $context       context data
     * @param   int                    $propertySize  number of properties
     * @param   bool                   $stripAccess   optional  whether to strip access informations from member names or nor
     * @return  array<string,mixed>
     */
    protected function unserializeProperties(stubPHPSerializer $serializer, stubPHPSerializedData $serialized, array $context, $propertySize, $stripAccess = false)
    {
        $properties = array();
        for ($i = 0; $i < $propertySize; $i++) {
            $propertyName = $serializer->unserialize($serialized, $context);
            if (true === $stripAccess && "\0" == $propertyName{0}) {
                $propertyName = $this->removeAccessInfoFromPropertyName($propertyName);
            }
            
            $properties[$propertyName] = $serializer->unserialize($serialized, $context);
        }
        
        return $properties;
    }
}
?>