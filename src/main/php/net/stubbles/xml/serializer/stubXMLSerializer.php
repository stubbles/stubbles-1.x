<?php
/**
 * Serializes arbitrary data except resources to xml.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @version     $Id: stubXMLSerializer.php 2977 2011-02-07 18:55:46Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjector',
                      'net::stubbles::reflection::stubReflectionObject',
                      'net::stubbles::xml::stubXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubAnnotationBasedObjectXmlSerializer'
);
/**
 * Serializes arbitrary data except resources to xml.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 */
class stubXMLSerializer extends stubBaseObject
{
    /**
     * injector to create object serializer instances
     *
     * @var  stubInjector
     */
    protected $injector;

    /**
     * constructor
     *
     * @param  stubInjector  $injector
     * @Inject
     */
    public function  __construct(stubInjector $injector)
    {
        $this->injector = $injector;
    }

    /**
     * serialize any data structure to xml
     *
     * @param   mixed                $value           data to serialize
     * @param   stubXMLStreamWriter  $xmlWriter       xml writer to write serialized data into
     * @param   string               $tagName         optional  name of the surrounding xml tag
     * @param   string               $elementTagName  optional  recurring element tag name for lists
     * @return  stubXMLStreamWriter
     */
    public function serialize($value, stubXMLStreamWriter $xmlWriter, $tagName = null, $elementTagName = null)
    {
        switch (gettype($value)) {
            case 'NULL':
                $this->serializeNull($xmlWriter, $tagName);
                break;

            case 'boolean':
                $this->serializeBool($value, $xmlWriter, $tagName);
                break;

            case 'string':
            case 'integer':
            case 'double':
                $this->serializeScalarValue($value, $xmlWriter, $tagName);
                break;

            case 'array':
                $this->serializeArray($value, $xmlWriter, $tagName, $elementTagName);
                break;

            case 'object':
                if ($value instanceof Iterator) {
                    $this->serializeArray($value, $xmlWriter, $tagName, $elementTagName);
                } else {
                    $this->serializeObject($value, $xmlWriter, $tagName);
                }
                break;

            default:
                // nothing to do
        }

        return $xmlWriter;
    }

    /**
     * serializes null to xml
     *
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeNull(stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = 'null';
        }

        $xmlWriter->writeStartElement($tagName);
        $xmlWriter->writeElement('null');
        $xmlWriter->writeEndElement();
        return $xmlWriter;
    }

    /**
     * serializes boolean value to xml
     *
     * @param   bool                 $value
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeBool($value, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = 'boolean';
        }

        return $this->serializeScalarValue((true === $value ? 'true' : 'false'), $xmlWriter, $tagName);
    }

    /**
     * serializes string to xml
     *
     * @param   string               $value
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeString($value, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes integer to xml
     *
     * @param   int                  $value
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeInt($value, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes float value to xml
     *
     * @param   float                $value
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeFloat($value, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        return $this->serializeScalarValue($value, $xmlWriter, $tagName);
    }

    /**
     * serializes any scalar value to xml
     *
     * @param   scalar               $value
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized value into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     */
    protected function serializeScalarValue($value, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        if (null === $tagName) {
            $tagName = gettype($value);
        }

        $xmlWriter->writeStartElement($tagName);
        $xmlWriter->writeText(strval($value));
        $xmlWriter->writeEndElement();
        return $xmlWriter;
    }

    /**
     * serializes an array to xml
     *
     * @param   array                $array           array to serialize
     * @param   stubXMLStreamWriter  $xmlWriter       xml writer to write serialized array into
     * @param   string               $tagName         optional  name of the surrounding xml tag
     * @param   string               $elementTagName  optional  necurring element tag name for lists
     * @return  stubXMLStreamWriter
     * @since   1.6.0
     */
    public function serializeArray($array, stubXMLStreamWriter $xmlWriter, $tagName = null, $elementTagName = null)
    {
        if (null === $tagName) {
            $tagName = 'array';
        }

        if (false !== $tagName) {
            $xmlWriter->writeStartElement($tagName);
        }

        foreach ($array as $key => $value) {
            if (is_int($key) === true) {
                $this->serialize($value, $xmlWriter, $elementTagName);
            } else {
                $this->serialize($value, $xmlWriter, $key);
            }
        }

        if (false !== $tagName) {
            $xmlWriter->writeEndElement();
        }

        return $xmlWriter;
    }

    /**
     * serializes an object to xml
     *
     * @param   object               $object     object to serialize
     * @param   stubXMLStreamWriter  $xmlWriter  xml writer to write serialized object into
     * @param   string               $tagName    optional  name of the surrounding xml tag
     * @return  stubXMLStreamWriter
     * @since   1.6.0
      */
    public function serializeObject($object, stubXMLStreamWriter $xmlWriter, $tagName = null)
    {
        $this->getObjectSerializer($object)->serialize($object, $this, $xmlWriter, $tagName);
        return $xmlWriter;
    }

    /**
     * returns serializer for given object
     *
     * @param   object                   $object
     * @return  stubXmlObjectSerializer
     */
    protected function getObjectSerializer($object)
    {
        $objectClass = new stubReflectionObject($object);
        if ($objectClass->hasAnnotation('XmlSerializer') === false) {
            return stubAnnotationBasedObjectXmlSerializer::forClass($objectClass);
        }

        return $this->injector->getInstance($objectClass->getAnnotation('XmlSerializer')
                                                        ->getSerializerClass()
                                                        ->getFullQualifiedClassName()
               );
    }
}
?>