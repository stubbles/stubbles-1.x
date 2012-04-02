<?php
/**
 * Base class for enums.
 *
 * @author      Frank Kleine  <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  lang
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException');
/**
 * Base class for enums.
 *
 * @package     stubbles
 * @subpackage  lang
 */
abstract class stubEnum extends stubBaseObject
{
    /**
     * name of the enum
     *
     * @var  string
     */
    protected $name;
    /**
     * value of enum
     *
     * @var  int
     */
    protected $value;

    /**
     * constructor
     *
     * @param  string  $name
     * @param  mixed   $value  optional
     */
    protected function __construct($name, $value = null)
    {
        $this->name  = $name;
        $this->value = ((null !== $value) ? ($value) : ($name));
    }

    /**
     * forbid cloning of enums
     *
     * @throws  stubRuntimeException
     */
    public final function __clone()
    {
        throw new stubRuntimeException('Cloning of enums is not allowed.');
    }

    /**
     * returns the name of the enum
     *
     * @return  string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * returns the value of the enum
     *
     * @return  mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * returns the enum instance of given class identified by its name
     *
     * @param   ReflectionClass  $enum
     * @param   string           $name
     * @return  stubEnum
     * @throws  stubIllegalArgumentException
     */
    public static function forName(ReflectionClass $enum, $name)
    {
        if ($enum->isSubclassOf(__CLASS__) === false) {
            throw new stubIllegalArgumentException('Given class is not an instance of ' . stubClassLoader::getFullQualifiedClassName(__CLASS__));
        }
        
        try {
            return $enum->getStaticPropertyValue($name);
        } catch (ReflectionException $re) {
            try {
                return $enum->getStaticPropertyValue(strtoupper($name));
            } catch (ReflectionException $re) {
                throw new stubIllegalArgumentException($re->getMessage());
            }
        }
    }

    /**
     * returns the enum instance of given class identified by its value
     *
     * @param   ReflectionClass  $enum
     * @param   string           $value
     * @return  stubEnum
     * @throws  stubIllegalArgumentException
     */
    public static function forValue(ReflectionClass $enum, $value)
    {
        if ($enum->isSubclassOf(__CLASS__) === false) {
            throw new stubIllegalArgumentException('Given class is not an instance of ' . stubClassLoader::getFullQualifiedClassName(__CLASS__));
        }
        
        try {
            foreach ($enum->getStaticProperties() as $instance) {
                if ($instance->value() === $value) {
                    return $instance;
                }
            }
        } catch (ReflectionException $re) {
            throw new stubIllegalArgumentException($re->getMessage());
        }
        
        throw new stubIllegalArgumentException('Enum ' . stubClassLoader::getFullQualifiedClassName($enum->getName()) . ' for value ' . $value . ' does not exist.');
    }

    /**
     * returns a list of all instances for given enum
     *
     * @param   ReflectionClass  $enum
     * @return  array<$enum->getName()>
     * @throws  stubIllegalArgumentException
     */
    public static function instances(ReflectionClass $enum)
    {
        if ($enum->isSubclassOf(__CLASS__) === false) {
            throw new stubIllegalArgumentException('Given class is not an instance of ' . stubClassLoader::getFullQualifiedClassName(__CLASS__));
        }
        
        return array_values($enum->getStaticProperties());
    }

    /**
     * returns a list of enum names for given enum
     *
     * @param   ReflectionClass  $enum
     * @return  array<string>
     * @throws  stubIllegalArgumentException
     */
    public static function namesOf(ReflectionClass $enum)
    {
        if ($enum->isSubclassOf(__CLASS__) === false) {
            throw new stubIllegalArgumentException('Given class is not an instance of ' . stubClassLoader::getFullQualifiedClassName(__CLASS__));
        }
        
        return array_keys($enum->getStaticProperties());
    }

    /**
     * returns a list of values for given enum
     *
     * @param   ReflectionClass      $enum
     * @return  array<string,mixed>
     * @throws  stubIllegalArgumentException
     */
    public static function valuesOf(ReflectionClass $enum)
    {
        if ($enum->isSubclassOf(__CLASS__) === false) {
            throw new stubIllegalArgumentException('Given class is not an instance of ' . stubClassLoader::getFullQualifiedClassName(__CLASS__));
        }
        
        $values = array();
        foreach ($enum->getStaticProperties() as $name => $instance) {
            $values[$name] = $instance->value;
        }
        
        return $values;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     * @XMLIgnore
     */
    public function equals($compare)
    {
        if ($compare instanceof stubEnum) {
            return ($compare->getClassName() === $this->getClassName() && $compare->name() === $this->name);
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     * @XMLIgnore
     */
    public function __toString()
    {
        $string  = $this->getClassName() . " {\n";
        $string .= '    ' . $this->name . "\n";
        $string .= '    ' . $this->value . "\n";
        $string .= "}\n";
        return $string;
    }
}
?>