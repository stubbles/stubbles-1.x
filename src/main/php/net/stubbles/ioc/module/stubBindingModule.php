<?php
/**
 * Interface for modules which configure the binder.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 * @version     $Id: stubBindingModule.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');
/**
 * Interface for modules which configure the binder.
 *
 * @package     stubbles
 * @subpackage  ioc_module
 */
interface stubBindingModule extends stubObject
{
    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder);
}
?>