<?php
/**
 * Interface for mapping the serializing of objects.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @version     $Id: stubPHPSerializerMapping.php 3264 2011-12-05 12:56:16Z mikey $
 */
/**
 * Interface for mapping the serializing of objects.
 * 
 * A serializer mapping takes control over how an object is serialized and
 * unserialized. It is responsible for one specific class.
 * 
 * Taken from the XP frameworks's interface remote.protocol.SerializerMapping.
 * 
 * @package     stubbles
 * @subpackage  php_serializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
interface stubPHPSerializerMapping extends stubObject
{
    /**
     * returns the token to be used by this mapping
     *
     * The token may be any string, but a simple char is preferred to keep
     * descriptional data within the serialized data small.
     *
     * @return  string
     */
    public function getToken();

    /**
     * return reflection instance of mapped class
     *
     * @return  ReflectionClass
     */
    public function getHandledClass();

    /**
     * Returns an on-the-wire representation of the given object
     *
     * @param   stubPHPSerializer    $serializer
     * @param   object               $object
     * @param   array<string,mixed>  $context     optional  context data
     * @return  string
     */
    public function serialize(stubPHPSerializer $serializer, $object, array $context = array());

    /**
     * returns a value for the given serialized string
     *
     * @param   stubPHPSerializer      $serializer  the serializer instance
     * @param   stubPHPSerializedData  $serialized  the serialized data
     * @param   array<string,mixed>    $context     optional  context data
     * @return  mixed
     */
    public function unserialize(stubPHPSerializer $serializer, stubPHPSerializedData $serialized, array $context = array());
}
?>