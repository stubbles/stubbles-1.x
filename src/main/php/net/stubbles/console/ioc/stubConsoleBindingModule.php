<?php
/**
 * Binding module for console classes.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubConsoleBindingModule.php 2250 2009-06-23 12:32:33Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleInputStream',
                      'net::stubbles::console::stubConsoleOutputStream',
                      'net::stubbles::ioc::module::stubBindingModule'
);
/**
 * Binding module for console classes.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubConsoleBindingModule extends stubBaseObject implements stubBindingModule
{
    /**
     * configure the binder
     *
     * @param  stubBinder  $binder
     */
    public function configure(stubBinder $binder)
    {
        $binder->bind('stubInputStream')
               ->named('stdin')
               ->toInstance(stubConsoleInputStream::forIn());
        $binder->bind('stubOutputStream')
               ->named('stdout')
               ->toInstance(stubConsoleOutputStream::forOut());
        $binder->bind('stubOutputStream')
               ->named('stderr')
               ->toInstance(stubConsoleOutputStream::forError());
        $binder->bind('stubExecutor')
               ->to('net::stubbles::console::stubConsoleExecutor');
    }
}
?>