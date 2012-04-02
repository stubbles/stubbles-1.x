<?php
/**
 * Serialized representation of a stubObject.
 *
 * @package     stubbles
 * @subpackage  lang_serialize
 * @version     $Id: stubSerializedObject.php 3273 2011-12-09 15:07:44Z mikey $
 */
/**
 * Serialized representation of a stubObject.
 * 
 * Using this allows lazy loading of classes stored in session.
 * 
 * @package     stubbles
 * @subpackage  lang_serialize
 */
class stubSerializedObject implements stubObject
{
    /**
     * full qualified class name of the serialized class
     *
     * @var  string
     */
    protected $fqClassName;
    /**
     * non qualified class name
     *
     * @var  string
     */
    protected $nqClassName;
    /**
     * the instance of the serialized class
     *
     * @var  stubObject
     */
    protected $classInstance;
    /**
     * hash code of serialized class
     *
     * @var  string
     */
    protected $hashCode;
    /**
     * the serialized class data
     *
     * @var  string
     */
    protected $data;

    /**
     * constructor
     *
     * @param  stubSerializable  $serializable  the instance to serialize
     */
    public function __construct(stubSerializable $serializable)
    {
        $this->fqClassName = $serializable->getClassName();
        if (null != $this->fqClassName) {
            $this->nqClassName = stubClassLoader::getNonQualifiedClassName($this->fqClassName);
        } else {
            $this->nqClassName = get_class($serializable);
        }
        
        $this->classInstance = $serializable;
        $this->hashCode      = $serializable->hashCode();
    }

    /**
     * interceptor called before object instance is serialized
     *
     * @return  array
     */
    public function __sleep()
    {
        if (null == $this->data && null != $this->classInstance) {
            $this->data = serialize($this->classInstance);
        }
        
        return array('fqClassName', 'nqClassName', 'hashCode', 'data');
    }

    /**
     * returns an unserialized version of the class
     * 
     * If the class was not loaded before it is loaded before unserialization.
     *
     * @return  stubSerializable
     */
    public function getUnserialized()
    {
        if (null != $this->classInstance) {
            return $this->classInstance;
        }
        
        if (class_exists($this->nqClassName, false) == false) {
            stubClassLoader::load($this->fqClassName);
        }
        
        $this->classInstance = unserialize($this->data);
        return $this->classInstance;
    }

    /**
     * returns the class name of the serialized class
     *
     * @return  string
     */
    public function getSerializedClassName()
    {
        return $this->fqClassName;
    }

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
        return stubClassLoader::getFullQualifiedClassName(__CLASS__);
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
     */
    public function hashCode()
    {
        return 'serialized: ' . $this->hashCode;
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
        if ($compare instanceof self) {
            return $this->hashCode == $compare->hashCode;
        }
        
        return false;
    }

    /**
     * returns a serialized representation of the class
     *
     * There can never be a serialized representation of an already serialized
     * class, so this always throws a RuntimeException.
     *
     * @throws  RuntimeException
     * @XMLIgnore
     */
    public function getSerialized()
    {
        // do not throw a stubException to prevent cirular references
        throw new RuntimeException('Can not serialize a serialized ' . $this->getClassName() . ' representation of ' . $this->fqClassName);
    }

    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values. Per default, this method returns:
     * [fully-qualified-class-name] ' {' [members-and-value-list] '}'
     * <code>
     * net::stubbles::lang::serialize::stubSerializedObject {
     *     class(string): net:.stubbles::stubExample
     *     data(string): [serialized representation of net::stubbles::stubExample]
     * }
     * </code>
     *
     * @return  string
     * @XMLIgnore
     */
    public function __toString()
    {
        $string  = $this->getClassName() . " {\n";
        $string .= '    fqClassName(string): ' . $this->fqClassName . "\n";
        $string .= '    nqClassName(string): ' . $this->nqClassName . "\n";
        $string .= '    data(string): ' . serialize($this->classInstance) . "\n";
        $string .= "}\n";
        return $string;
    }
}
?>