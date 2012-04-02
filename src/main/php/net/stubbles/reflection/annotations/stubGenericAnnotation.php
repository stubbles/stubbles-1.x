<?php
/**
 * Generic implementation for all annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubMethodNotSupportedException',
                      'net::stubbles::reflection::annotations::stubAbstractAnnotation'
);
/**
 * Generic implementation for all annotations.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations
 * @since       1.6.0
 */
class stubGenericAnnotation extends stubAbstractAnnotation
{
    /**
     * list of properties of this annotation
     *
     * @var  array<string,mixed>
     */
    protected $properties       = array();
    /**
     * properties backup which can be serialized
     *
     * @var  array<string,scalar>
     */
    protected $propertiesBackup = array();

    /**
     * Returns the target of the annotation as bitmap.
     *
     * @return  int
     */
    public function getAnnotationTarget()
    {
        return stubAnnotation::TARGET_ALL;
    }

    /**
     * sets a single value
     *
     * @param  mixed  $value
     */
    public function setValue($value)
    {
        $this->properties['__value'] = $value;
    }

    /**
     * checks whether a value with given name exists
     *
     * Returns null if a value with given name does not exist or is not set.
     *
     * @param   string  $name
     * @return  bool
     * @since   1.7.0
     */
    public function hasValueByName($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * returns a value by its name
     *
     * Returns null if a value with given name does not exist or is not set.
     *
     * @param   string  $name
     * @return  mixed
     * @since   1.7.0
     */
    public function getValueByName($name)
    {
        if (isset($this->properties[$name]) === true) {
            return $this->properties[$name];
        }

        return null;
    }

    /**
     * sets value for given property
     *
     * @param  string  $name
     * @param  mixed   $value
     */
    public function  __set($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * responds to a method call of an undefined method
     *
     * @param   string  $name
     * @param   array   $arguments
     * @return  mixed
     * @throws  stubMethodNotSupportedException
     */
    public function  __call($name, $arguments)
    {
        if (isset($this->properties[$name]) === true) {
            return $this->properties[$name];
        }

        if (substr($name, 0, 3) === 'get') {
            return $this->getProperty(strtolower(substr($name, 3, 1)) . substr($name, 4),
                                      $this->extractDefaultValue($arguments)
                    );
        }

        if (substr($name, 0, 2) === 'is') {
            return $this->getBooleanProperty(strtolower(substr($name, 2, 1)) . substr($name, 3));
        }

        if (substr($name, 0, 3) === 'has') {
            return $this->hasProperty(strtolower(substr($name, 3, 1)) . substr($name, 4));
        }

        throw new stubMethodNotSupportedException('The method ' . $name . ' does not exit.');
    }

    /**
     * returns first value in array or null if it does not exist
     *
     * @param   array  $arguments
     * @return  miced
     */
    protected function extractDefaultValue(array $arguments)
    {
        if (count($arguments) === 0) {
            return null;
        }

        return array_shift($arguments);
    }

    /**
     * returns property which is retrieved via get$PROPERTYNAME()
     *
     * @param   string  $propertyName
     * @param   mixed   $defaultValue
     * @return  mixed
     */
    protected function getProperty($propertyName, $defaultValue)
    {
        if (count($this->properties) === 1 && isset($this->properties['__value']) === true) {
            return $this->properties['__value'];
        }

        if (isset($this->properties[$propertyName]) === true) {
            return $this->properties[$propertyName];
        }

        return $defaultValue;
    }

    /**
     * returns boolean property which is retrieved via is$PROPERTYNAME()
     *
     * @param   string  $propertyName
     * @return  bool
     */
    protected function getBooleanProperty($propertyName)
    {
        if (count($this->properties) === 1 && isset($this->properties['__value']) === true) {
            return $this->properties['__value'];
        }

        if (isset($this->properties[$propertyName]) === true) {
            return $this->properties[$propertyName];
        }

        return false;
    }

    /**
     * checks if property which is checked via has$PROPERTYNAME() is set
     *
     * @param   string  $propertyName
     * @return  bool
     */
    protected function hasProperty($propertyName)
    {
        if (count($this->properties) === 1
          && isset($this->properties['__value']) === true
          && 'value' === $propertyName) {
            return isset($this->properties['__value']);
        }

        return isset($this->properties[$propertyName]);
    }

    /**
     * template method to hook into __sleep()
     *
     * @return  array<string>  list of property names that should not be serialized
     */
    protected function __doSleep()
    {
        $this->propertiesBackup = array();
        foreach ($this->properties as $propertyName => $value) {
            if ($value instanceof stubReflectionClass) {
                $this->propertiesBackup[$propertyName] = $value->getFullQualifiedClassName() . '.class';
            } else {
                $this->propertiesBackup[$propertyName] = $value;
            }
        }

        return array('properties');
    }

    /**
     * template method to hook into __wakeup()
     */
    protected function __doWakeUp()
    {
        foreach ($this->propertiesBackup as $propertyName => $value) {
            if (substr($value, -6) === '.class') {
                $this->properties[$propertyName] = new stubReflectionClass(substr($value, 0, -6));
            } else {
                $this->properties[$propertyName] = $value;
            }
        }

        $this->propertiesBackup = array();
    }
}
?>