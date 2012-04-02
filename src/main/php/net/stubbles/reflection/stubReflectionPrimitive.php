<?php
/**
 * Type reference for primitives.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  reflection
 */
stubClassLoader::load('net::stubbles::lang::stubEnum',
                      'net::stubbles::reflection::stubReflectionType'
);
/**
 * Type reference for primitives.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  reflection
 */
class stubReflectionPrimitive extends stubEnum implements stubReflectionType
{
    /**
     * primitive of type string
     *
     * @var  stubReflectionPrimitive
     */
    public static $STRING;
    /**
     * primitive of type int
     *
     * @var  stubReflectionPrimitive
     */
    public static $INT;
    /**
     * primitive of type int, marked as integer
     *
     * @var  stubReflectionPrimitive
     */
    public static $INTEGER;
    /**
     * primitive of type float
     *
     * @var  stubReflectionPrimitive
     */
    public static $FLOAT;
    /**
     * primitive of type double, equal to float
     *
     * @var  stubReflectionPrimitive
     */
    public static $DOUBLE;
    /**
     * primitive of type bool
     *
     * @var  stubReflectionPrimitive
     */
    public static $BOOL;
    /**
     * primitive of type bool, marked as boolean
     *
     * @var  stubReflectionPrimitive
     */
    public static $BOOLEAN;
    /**
     * primitive of type array
     *
     * @var  stubReflectionPrimitive
     */
    public static $ARRAY;

    /**
     * static initializing
     */
    // @codeCoverageIgnoreStart
    public static function __static()
    {
        self::$STRING  = new self('string', 'string');
        self::$INT     = new self('int', 'int');
        self::$INTEGER = new self('integer', 'int');
        self::$FLOAT   = new self('float', 'float');
        self::$DOUBLE  = new self('double', 'float');
        self::$BOOL    = new self('bool', 'bool');
        self::$BOOLEAN = new self('boolean', 'bool');
        self::$ARRAY   = new self('array', 'array');
    }
    // @codeCoverageIgnoreEnd

    /**
     * returns the enum instance of given class identified by its name
     *
     * @param   ReflectionClass  $enum
     * @param   string           $name
     * @return  stubEnum
     */
    public static function forName(ReflectionClass $enum, $name)
    {
        if (substr(strtolower($name), 0, 5) == 'array') {
            return stubEnum::forName($enum, 'ARRAY');
        }
        
        return stubEnum::forName($enum, $name);
    }

    /**
     * returns the name of the type
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name();
    }

    /**
     * checks whether the type is an object
     *
     * @return  bool
     */
    public function isObject()
    {
        return false;
    }

    /**
     * checks whether the type is a primitive
     *
     * @return  bool
     */
    public function isPrimitive()
    {
        return true;
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($compare->value == $this->value);
        }
        
        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString()
    {
        return 'net::stubbles::reflection::stubReflectionPrimitive[' . $this->value . "] {\n}\n";
    }
}
?>