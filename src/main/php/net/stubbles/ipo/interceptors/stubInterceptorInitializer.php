<?php
/**
 * Interface for initializing the interceptors.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @version     $Id: stubInterceptorInitializer.php 3149 2011-08-09 21:04:00Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPostInterceptor',
                      'net::stubbles::ipo::interceptors::stubPreInterceptor',
                      'net::stubbles::lang::initializer::stubInitializer'
);
/**
 * Interface for initializing the interceptors.
 *
 * @package     stubbles
 * @subpackage  ipo_interceptors
 * @deprecated  use webapp configuration instead, will be removed with 1.8.0 or 2.0.0
 */
interface stubInterceptorInitializer extends stubInitializer
{
    /**
     * sets the descriptor that identifies the initializer
     *
     * @param   string                      $descriptor
     * @return  stubInterceptorInitializer
     */
    public function setDescriptor($descriptor);

    /**
     * returns the list of pre interceptors
     *
     * @return  array<stubPreInterceptor>
     */
    public function getPreInterceptors();

    /**
     * returns the list of post interceptors
     *
     * @return  array<stubPostInterceptor>
     */
    public function getPostInterceptors();
}
?>