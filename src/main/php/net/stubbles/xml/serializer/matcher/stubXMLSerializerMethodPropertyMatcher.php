<?php
/**
 * Matcher for methods and properties.
 * 
 * @package     stubbles
 * @subpackage  xml_serializer_matcher
 * @version     $Id: stubXMLSerializerMethodPropertyMatcher.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::matcher::stubMethodMatcher',
                      'net::stubbles::reflection::matcher::stubPropertyMatcher'
);
/**
 * Matcher for methods and properties.
 * 
 * @package     stubbles
 * @subpackage  xml_serializer_matcher
 */
class stubXMLSerializerMethodPropertyMatcher extends stubBaseObject implements stubMethodMatcher, stubPropertyMatcher
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(ReflectionMethod $method)
    {
        if ($method->isPublic() === false || $method->isStatic() === true) {
            return false;
        }
        
        if ($method->isConstructor() === true || $method->isDestructor() === true) {
            return false;
        }
        
        if (0 == strncmp($method->getName(), '__', 2)) {
            return false;
        }
        
        if (0 != $method->getNumberOfParameters()) {
            return false;
        }
        
        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   stubReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(stubReflectionMethod $method)
    {
        return ($method->hasAnnotation('XMLIgnore') !== true);
    }

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   ReflectionProperty  $property
     * @return  bool
     */
    public function matchesProperty(ReflectionProperty $property)
    {
        if ($property->isPublic() === false || $property->isStatic() === true) {
            return false;
        }
        
        return true;
    }

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   stubReflectionProperty  $property
     * @return  bool
     */
    public function matchesAnnotatableProperty(stubReflectionProperty $property)
    {
        return ($property->hasAnnotation('XMLIgnore') !== true);
    }
}
?>