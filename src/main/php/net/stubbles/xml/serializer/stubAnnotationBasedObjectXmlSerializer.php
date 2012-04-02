<?php
/**
 * Container for extracting informations on how to serialize a class.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 * @version     $Id: stubAnnotationBasedObjectXmlSerializer.php 3092 2011-03-15 20:54:47Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::serializer::stubObjectXmlSerializer',
                      'net::stubbles::xml::serializer::delegate::stubXmlSerializerAttributeDelegate',
                      'net::stubbles::xml::serializer::delegate::stubXmlSerializerFragmentDelegate',
                      'net::stubbles::xml::serializer::delegate::stubXmlSerializerTagDelegate',
                      'net::stubbles::xml::serializer::matcher::stubXMLSerializerMethodPropertyMatcher',
                      'net::stubbles::reflection::stubReflectionObject'
);
/**
 * Container for extracting informations on how to serialize a class.
 *
 * @package     stubbles
 * @subpackage  xml_serializer
 */
class stubAnnotationBasedObjectXmlSerializer extends stubBaseObject implements stubObjectXmlSerializer
{
    /**
     * default tag name for class
     *
     * @var  string
     */
    protected $classTagName;
    /**
     * list of properties to serialize
     *
     * @var  array<string,stubXmlSerializerDelegate>
     */
    protected $properties  = array();
    /**
     * list of methods to serialize
     *
     * @var  array<string,stubXmlSerializerDelegate>
     */
    protected $methods     = array();
    /**
     * reflection instance of class to serialize
     *
     * @var  stubBaseReflectionClass
     */
    protected $refClass;
    /**
     * the matcher to be used for methods and properties
     *
     * @var  stubXMLSerializerMethodPropertyMatcher
     */
    protected static $methodAndPropertyMatcher;
    /**
     * simple cache
     *
     * @var  array
     */
    protected static $cache = array();

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$methodAndPropertyMatcher = new stubXMLSerializerMethodPropertyMatcher();
    }

    /**
     * constructor
     *
     * It is recommended to not use the constructor but the static fromObject()
     * method. The constructor should be used if one is sure that there is only
     * one instance of a class to serialize.
     *
     * @param   stubBaseReflectionClass  $objectClass
     */
    public function __construct(stubBaseReflectionClass $objectClass)
    {
        $this->refClass = $objectClass;
        $this->extractProperties();
        $this->extractMethods();
    }

    /**
     * creates the structure from given object
     *
     * This method will cache the result - on the next request with the same
     * class it will return the same result, even if the given object is a
     * different instance.
     *
     * @param   stubBaseReflectionClass       $objectClass
     * @return  stubXMLSerializerObjectData
     */
    public static function forClass(stubBaseReflectionClass $objectClass)
    {
        $className = $objectClass->getFullQualifiedClassName();
        if (isset(self::$cache[$className]) === true) {
            return self::$cache[$className];
        }

        self::$cache[$className] = new self($objectClass);
        return self::$cache[$className];
    }

    /**
     * serializes given value
     *
     * @param  mixed                $value
     * @param  stubXMLSerializer    $xmlSerializer  serializer in case $value is not just a scalar value
     * @param  stubXMLStreamWriter  $xmlWriter      xml writer to write serialized object into
     * @param  string               $tagName        name of the surrounding xml tag
     */
    public function serialize($object, stubXMLSerializer $xmlSerializer, stubXMLStreamWriter $xmlWriter, $tagName)
    {
        $xmlWriter->writeStartElement($this->getClassTagName($tagName));
        foreach ($this->properties as $propertyName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize($object->$propertyName, $xmlSerializer, $xmlWriter);
        }

        foreach ($this->methods as $methodName => $xmlSerializerDelegate) {
            $xmlSerializerDelegate->serialize($object->$methodName(), $xmlSerializer, $xmlWriter);
        }

        $xmlWriter->writeEndElement();
    }

    /**
     * returns tag name for the class itself
     *
     * @param   string  $tagName  default tag name to be used
     * @return  string
     */
    protected function getClassTagName($tagName)
    {
        if (null !== $tagName) {
            return $tagName;
        }
        
        if ($this->refClass->hasAnnotation('XMLTag') === true) {
            return $this->refClass->getAnnotation('XMLTag')->getTagName();
        }

        return $this->refClass->getName();
    }

    /**
     * extract informations about properties
     */
    protected function extractProperties()
    {
        foreach ($this->refClass->getPropertiesByMatcher(self::$methodAndPropertyMatcher) as $property) {
            $this->properties[$property->getName()] = $this->createSerializerDelegate($property, $property->getName());
        }
    }

    /**
     * extract informations about methods
     */
    protected function extractMethods()
    {
        foreach ($this->refClass->getMethodsByMatcher(self::$methodAndPropertyMatcher) as $method) {
            $this->methods[$method->getName()] = $this->createSerializerDelegate($method, $method->getName());
        }
    }

    /**
     * extracts informations about annotated element
     *
     * @param   stubAnnotatable            $annotatable      the annotatable element to serialize
     * @param   string                     $annotatableName  name of annotatable element
     * @return  stubXmlSerializerDelegate
     */
    protected function createSerializerDelegate(stubAnnotatable $annotatable, $annotatableName)
    {
        if ($annotatable->hasAnnotation('XMLAttribute') === true) {
            $xmlAttribute = $annotatable->getAnnotation('XMLAttribute');
            return new stubXmlSerializerAttributeDelegate($xmlAttribute->getAttributeName(), $xmlAttribute->getSkipEmpty(true));
        } elseif ($annotatable->hasAnnotation('XMLFragment') === true) {
            $xmlFragment = $annotatable->getAnnotation('XMLFragment');
            return new stubXmlSerializerFragmentDelegate($xmlFragment->getTagName(), $xmlFragment->isTransformNewLineToBr());
        } elseif ($annotatable->hasAnnotation('XMLTag') === true) {
            $xmlTag = $annotatable->getAnnotation('XMLTag');
            return new stubXmlSerializerTagDelegate($xmlTag->getTagName(), $xmlTag->getElementTagName());
        }
        
        return new stubXmlSerializerTagDelegate($annotatableName);
    }
}
?>