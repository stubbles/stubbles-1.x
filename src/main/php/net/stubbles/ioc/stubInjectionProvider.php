<?php
/**
 * Interface for providers that create objects that are required by the
 * Inversion of Control features of Stubbles.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
/**
 * Interface for providers that create objects that are required by the
 * Inversion of Control features of Stubbles.
 *
 * @package     stubbles
 * @subpackage  ioc
 */
interface stubInjectionProvider extends stubObject
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null);
}
?>