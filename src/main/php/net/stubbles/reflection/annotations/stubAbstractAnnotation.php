<?php
/**
 * Abstract base class for annotations
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @version     $Id: stubAbstractAnnotation.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::stubAnnotation');
/**
 * Abstract base class for annotations
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 */
abstract class stubAbstractAnnotation extends stubSerializableObject implements stubAnnotation
{
    /**
     * The name of the annotation
     *
     * @var  string
     */
    protected $annotationName;
    /**
     * a list of reflected class instances used in the annotation
     * 
     * This property should only be used by this class. We can not make this
     * property private because the serializung mechanism will not work then.
     *
     * @var  array<string,string>
     */
    protected $reflectedClasses = array();

    /**
     * Sets the name under which the annotation is stored.
     *
     * @param  string  $name
     */
    public function setAnnotationName($name)
    {
        $this->annotationName = $name;
    }

    /**
     * Returns the name under which the annotation is stored.
     *
     * @return  string
     */
    public function getAnnotationName()
    {
        return $this->annotationName;
    }

    /**
     * do some last operations after all values have been set
     * 
     * This method may check if all required values have been set and throw
     * an exception if values are missing.
     *
     * @throws  ReflectionException
     */
    public function finish()
    {
        // intentionally empty
    }

    /**
     * assure that a clone clones all properties of type object as well
     */
    public function __clone()
    {
        foreach (get_object_vars($this) as $name => $value) {
            if ($value instanceof stubClonable) {
                $this->$name = clone $this->$name;
            }
        }
    }

    /**
     * template method to hook into __sleep()
     *
     * @return  array<string>  list of property names that should not be serialized
     */
    protected function __doSleep()
    {
        $this->reflectedClasses = array();
        return array();
    }

    /**
     * takes care of serializing the value
     *
     * @param  array   &$propertiesToSerialize  list of properties to serialize
     * @param  string  $name                    name of the property to serialize
     * @param  mixed   $value                   value to serialize
     */
    protected function __doSerialize(&$propertiesToSerialize, $name, $value)
    {
        if ($value instanceof stubReflectionClass) {
            $this->reflectedClasses[$name] = $value->getFullQualifiedClassName();
            return;
        }
        
        parent::__doSerialize($propertiesToSerialize, $name, $value);
    }

    /**
     * template method to hook into __wakeup()
     */
    protected function __doWakeUp()
    {
        foreach ($this->reflectedClasses as $propertyName => $reflectedClasses) {
            $this->$propertyName = new stubReflectionClass($reflectedClasses);
        }
        
        $this->reflectedClasses = array();
    }
}
?>