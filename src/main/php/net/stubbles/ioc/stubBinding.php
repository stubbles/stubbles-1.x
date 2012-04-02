<?php
/**
 * Binding to bind an interface to an implementation
 *
 * @package     stubbles
 * @subpackage  ioc
 * @version     $Id: stubBinding.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Binding to bind an interface to an implementation
 *
 * @package     stubbles
 * @subpackage  ioc
 */
interface stubBinding extends stubObject
{
    /**
     * set the name of the injection
     *
     * @param   string       $name
     * @return  stubBinding
     */
    public function named($name);

    /**
     * returns the created instance
     *
     * @param   string  $type
     * @param   string  $name
     * @return  mixed
     */
    public function getInstance($type, $name);

    /**
     * creates a unique key for this binding
     *
     * @return  string
     */
    public function getKey();
}
?>