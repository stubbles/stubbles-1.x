<?php
/**
 * Interface for processors.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 * @version     $Id: stubProcessor.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequest',
                      'net::stubbles::ipo::response::stubResponse',
                      'net::stubbles::ipo::session::stubSession',
                      'net::stubbles::webapp::stubUriRequest'
);
/**
 * Interface for processors.
 *
 * @package     stubbles
 * @subpackage  webapp_processor
 */
interface stubProcessor extends stubObject
{
    /**
     * operations to be done before the request is processed
     *
     * @param   stubUriRequest  $uriRequest
     * @return  stubProcessor
     */
    public function startup(stubUriRequest $uriRequest);

    /**
     * returns the required role of the user to be able to process the request
     *
     * The method should return <null> if no role is required at all. It should
     * return the value of $defaultRole if a role is required, but no special
     * role for the current request (i.e. processing the request requires a login,
     * but not any role). In any other case it should return the required role.
     *
     * @param   string  $defaultRole
     * @return  string
     */
    public function getRequiredRole($defaultRole);

    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName();

    /**
     * checks whether the current request requires ssl or not
     *
     * @return  bool
     */
    public function forceSsl();

    /**
     * checks whether the request is ssl or not
     *
     * @return  bool
     */
    public function isSsl();

    /**
     * checks whether document to generate is cachable or not
     *
     * @return  bool
     */
    public function isCachable();

    /**
     * returns a list of variables that have an influence on caching
     *
     * @return  array<string,scalar>
     */
    public function getCacheVars();

    /**
     * processes the request
     *
     * @return  stubProcessor
     */
    public function process();

    /**
     * operations to be done after the request was processed
     *
     * @return  stubProcessor
     */
    public function cleanup();
}
?>