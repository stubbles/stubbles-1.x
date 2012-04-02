<?php
/**
 * Interface for all scopes
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBindingScope.php 3012 2011-02-17 14:19:49Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubInjectionProvider',
                      'net::stubbles::reflection::stubBaseReflectionClass'
);
/**
 * Interface for all scopes
 *
 * @package     stubbles
 * @subpackage  ioc
 */
interface stubBindingScope extends stubObject
{
    /**
     * returns the requested instance from the scope
     *
     * @param   stubBaseReflectionClass  $type      type of the object
     * @param   stubBaseReflectionClass  $impl      concrete implementation
     * @param   stubInjectionProvider    $provider
     * @return  object
     */
    public function getInstance(stubBaseReflectionClass $type, stubBaseReflectionClass $impl, stubInjectionProvider $provider);
}
?>