<?php
/**
 * Interface for initializers of all kind.
 *
 * @package     stubbles
 * @subpackage  lang_initializer
 * @version     $Id: stubInitializer.php 3255 2011-12-02 12:26:00Z mikey $
 */
/**
 * Interface for initializers of all kind.
 *
 * @package     stubbles
 * @subpackage  lang_initializer
 * @deprecated  will be removed with 1.8.0 or 2.0.0
 */
interface stubInitializer extends stubObject
{
    /**
     * initializing method
     *
     * @return  stubInitializer
     */
    public function init();
}
?>