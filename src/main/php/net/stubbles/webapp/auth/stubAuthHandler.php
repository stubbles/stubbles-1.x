<?php
/**
 * Interface for authentication handlers.
 * 
 * @package     stubbles
 * @subpackage  webapp_auth
 * @version     $Id: stubAuthHandler.php 3170 2011-08-23 15:00:43Z mikey $
 */
/**
 * Interface for authentication handlers.
 * 
 * @package     stubbles
 * @subpackage  webapp_auth
 */
interface stubAuthHandler extends stubObject
{
    /**
     * checks if given role required login
     *
     * @param   string  $role
     * @return  bool
     */
    public function requiresLogin($role);

    /**
     * returns login url
     *
     * @return  string
     */
    public function getLoginUrl();

    /**
     * checks whether the auth handler has a user
     *
     * @return  bool
     */
    public function hasUser();

    /**
     * returns a default role
     *
     * @return  string
     */
    public function getDefaultRole();

    /**
     * checks if user has a specific role
     *
     * @param   string  $role
     * @return  bool
     */
    public function userHasRole($role);
}
?>