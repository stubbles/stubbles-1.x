<?php
/**
 * Scope for singletons
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBindingScopeSingleton.php 3012 2011-02-17 14:19:49Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBindingScope');
/**
 * Scope for singletons
 *
 * @package     stubbles
 * @subpackage  ioc
 */
class stubBindingScopeSingleton extends stubBaseObject implements stubBindingScope
{
    /**
     * instances in this scope
     *
     * @var  array<string,object>
     */
    protected $instances = array();

    /**
     * returns the requested instance from the scope
     *
     * @param   stubBaseReflectionClass  $type      type of the object
     * @param   stubBaseReflectionClass  $impl      concrete implementation
     * @param   stubInjectionProvider    $provider
     * @return  object
     */
    public function getInstance(stubBaseReflectionClass $type, stubBaseReflectionClass $impl, stubInjectionProvider $provider)
    {
        $key = $impl->getName();
        if (isset($this->instances[$key]) === false) {
            $this->instances[$key] = $provider->get();
        }
        
        return $this->instances[$key];
    }
}
?>