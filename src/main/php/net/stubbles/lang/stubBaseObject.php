<?php
/**
 * base class for all other stubbles classes except static ones and classes
 * extending php built-in classes
 *
 * @author      Frank Kleine  <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  lang
 */
/**
 * base class for all other stubbles classes except static ones and classes
 * extending php built-in classes
 *
 * @package     stubbles
 * @subpackage  lang
 */
class stubBaseObject implements stubObject
{
    /**
     * returns class informations
     *
     * @return  stubReflectionObject
     * @XMLIgnore
     */
    public function getClass()
    {
        stubClassLoader::load('net::stubbles::reflection::stubReflectionObject');
        $refObject = new stubReflectionObject($this);
        return $refObject;
    }

    /**
     * returns package informations
     *
     * @return  stubReflectionPackage
     * @XMLIgnore
     */
    public function getPackage()
    {
         stubClassLoader::load('net::stubbles::reflection::stubReflectionPackage');
         $refPackage = new stubReflectionPackage(stubClassLoader::getPackageName($this->getClassName()));
         return $refPackage;
    }

    /**
     * returns the full qualified class name
     *
     * @return  string
     * @XMLIgnore
     */
    public function getClassName()
    {
        return stubClassLoader::getFullQualifiedClassName(get_class($this));
    }

    /**
     * returns the name of the package where the class is inside
     *
     * @return  string
     * @XMLIgnore
     */
    public function getPackageName()
    {
        return stubClassLoader::getPackageName($this->getClassName());
    }

    /**
     * returns a unique hash code for the class
     *
     * @return  string
     * @XMLIgnore
     */
    public function hashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof stubObject) {
            return ($this->hashCode() == $compare->hashCode());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example::MyClass {
     *     foo(string): hello
     *     bar(example::AnotherClass): example::AnotherClass {
     *         baz(int): 5
     *     }
     * }
     * </code>
     *
     * @return  string
     * @XMLIgnore
     */
    public function __toString()
    {
        return self::getStringRepresentationOf($this, self::_extractProperties($this));
    }

    /**
     * helper method to extract all properties regardless of their visibility
     *
     * This is a workaround for the problem that as of PHP 5.2.4 get_object_vars()
     * is not any more capable of retrieving private properties from child classes.
     * See http://stubbles.org/archives/32-Subtle-BC-break-in-PHP-5.2.4.html.
     *
     * @param   mixed  $object
     * @return  array<string,mixed>
     */
    protected static function _extractProperties($object)
    {
        $properties      = (array) $object;
        $fixedProperties = array();
        foreach ($properties as $propertyName => $propertyValue) {
            if (strstr($propertyName, "\0") === false) {
                $fixedProperties[$propertyName] = $propertyValue;
                continue;
            }
            
            $fixedProperties[substr($propertyName, strrpos($propertyName, "\0") + 1)] = $propertyValue;
        }
        
        return $fixedProperties;
    }

    /**
     * returns a string representation of the class
     *
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * example::MyClass {
     *     foo(string): hello
     *     bar(example::AnotherClass): example::AnotherClass {
     *         baz(int): 5
     *     }
     * }
     * </code>
     * Please note that protected and private properties of the class wil only be
     * in the result if the second argument contains a list of the properties and
     * its values. If not set only public properties can be extracted due to the
     * behaviour of get_object_vars().
     *
     * @param   stubObject  $object      the object to convert to a string
     * @param   array       $properties  optional  the properties, if not set they will be retrieved
     * @return  string
     * @XMLIgnore
     */
    public static function getStringRepresentationOf(stubObject $object, array $properties = null)
    {
        if (null === $properties) {
            $properties = self::_extractProperties($object);
        }

        $string = $object->getClassName() . " {\n";
        foreach ($properties as $name => $value) {
            if ('_serializedProperties' == $name) {
                continue;
            }

            $string .= '    ' . $name . '(' . self::_determineType($value) . '): ';
            if (($value instanceof self) == false) {
                $string .= $value . "\n";
                continue;
            }

            $lines       = explode("\n", (string) $value);
            $lineCounter = 0;
            foreach ($lines as $line) {
                if (empty($line) == true) {
                    continue;
                }

                if (0 != $lineCounter) {
                    $string .= '    ' . $line . "\n";
                } else {
                    $string .= $line . "\n";
                }

                $lineCounter++;
            }
        }

        $string .= "}\n";
        return $string;
    }

    /**
     * determines the correct type of a value
     *
     * @param   mixed   &$value
     * @return  string
     */
    private static function _determineType(&$value)
    {
        if (is_object($value) === false) {
            if (is_resource($value) === false) {
                return gettype($value);
            }

            return 'resource[' . get_resource_type($value) . ']';
        }

        if ($value instanceof stubObject) {
            return $value->getClassName();
        }

        return get_class($value);
    }
}
?>