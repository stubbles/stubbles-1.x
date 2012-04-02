<?php
/**
 * Class for serialization of objects that implement the SPL Serializable interface.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubPHPSerializerSPLSerializableMapping.php 3264 2011-12-05 12:56:16Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::php::serializer::stubPHPSerializerMapping'
);
/**
 * Class for serialization of objects that implement the SPL Serializable interface.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @link        http://www.php.net/~helly/php/ext/spl/interfaceSerializable.html
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
class stubPHPSerializerSPLSerializableMapping extends stubBaseObject implements stubPHPSerializerMapping
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
        return 'C';
    }

    /**
     * return reflection instance of mapped class
     *
     * @return  ReflectionClass
     */
    public function getHandledClass()
    {
        if (null === self::$handledClass) {
            self::$handledClass = new ReflectionClass('Serializable');
        }
        
        return self::$handledClass;
    }

    /**
     * Returns an on-the-wire representation of the given object
     *
     * @param   stubPHPSerializer    $serializer
     * @param   object               $object
     * @param   array<string,mixed>  $context     optional  context data
     * @return  string
     * @throws  stubIllegalArgumentException
     */
    public function serialize(stubPHPSerializer $serializer, $object, array $context = array())
    {
        if (($object instanceof Serializable) === false) {
            throw new stubIllegalArgumentException($this->getClassName() . ' can only serialize objects of instance Serializable.');
        }
        
        return serialize($object);
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
        $offset    = $serialized->getOffset();
        $serialized->moveOffset(2); // token
        $className = $serialized->consumeString();
        $size      = (int) $serialized->consumeSize();
        //                      C   :   info about classname length  :   classname          in "  :   info about data size : data in {}
        $endOffset = ($offset + 1 + 1 + strlen(strlen($className)) + 1 + strlen($className) + 2 + 1 + strlen($size) + 1 + $size + 2);
        $data      = $serialized->getSubData($offset, $endOffset);
        $serialized->moveOffset($size + strlen($className) + 1); // closing "}"
        $instance = @unserialize($data);
        if (false === $instance) {
            throw new stubFormatException('Cannot unserialize type "' . $className . '" (' . $serialized . '), contains invalid serialized data.');
        }
        
        return $instance;
    }
}
?>