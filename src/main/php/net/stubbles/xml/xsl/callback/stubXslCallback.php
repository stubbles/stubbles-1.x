<?php
/**
 * Class to register classes and make their methods available as callback in xsl.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslCallback.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::xsl::callback::stubXslCallbackException');
/**
 * Class to register classes and make their methods available as callback in xsl.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
class stubXslCallback extends stubBaseObject
{
    /**
     * list of callback instances
     *
     * @var  array<string,stubObject>
     */
    protected $callbacks = array();

    /**
     * register a new instance as callback
     *
     * @param  string      $name      name to register the callback under
     * @param  stubObject  $callback
     */
    public function setCallback($name, stubObject $callback)
    {
        $this->callbacks[$name] = $callback;
    }

    /**
     * check if a callback exists for the given name
     *
     * @param   string  $name  name the callback is registered under
     * @return  bool
     */
    public function hasCallback($name)
    {
        return isset($this->callbacks[$name]);
    }

    /**
     * returns the callback with the given name
     *
     * @param   string      $name  name the callback is registered under
     * @return  stubObject
     */
    public function getCallback($name)
    {
        if (isset($this->callbacks[$name]) == true) {
            return $this->callbacks[$name];
        }
        
        return null;
    }

    /**
     * returns list of callbacks
     *
     * @return  array<string,stubObject>
     * @since   1.5.0
     */
    public function getCallbacks()
    {
        return $this->callbacks;
    }

    /**
     * invoke a method on a callback class
     *
     * @param   string  $name        name of callback instance to call method on
     * @param   string  $methodName  name of method to call
     * @param   array   $arguments   list of arguments for method to call
     * @return  mixed
     * @throws  stubXslCallbackException
     */
    public function invoke($name, $methodName, array $arguments = array())
    {
        if ($this->hasCallback($name) == false) {
            throw new stubXslCallbackException('A callback with the name ' . $name . ' does not exist.');
        }

        $callback   = $this->getCallback($name);
        $class      = $callback->getClass();
        if ($class->hasMethod($methodName) === false) {
            throw new stubXslCallbackException('Callback with name ' . $name . ' does not have a method named ' . $methodName);
        }
        
        $method = $class->getMethod($methodName);
        if ($method->hasAnnotation('XslMethod') === false) {
            throw new stubXslCallbackException('The callback\'s ' . $name . ' ' . $callback->getClassName() . '::' . $methodName . '() is not annotated as XslMethod.');
        }
        
        if ($method->isPublic() === false) {
            throw new stubXslCallbackException('The callback\'s ' . $name . ' ' . $callback->getClassName() . '::' . $methodName . '() is not a public method.');
        }
        
        if ($method->isStatic() === true) {
            return $method->invokeArgs(null, $arguments);
        }
        
        return $method->invokeArgs($callback, $arguments);
    }
}
?>