<?php
/**
 * This variant type is only used to include variants in the
 * configuration, that can only be set from php code.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubDummyVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubAbstractVariant');
/**
 * This variant type is only used to include variants in the
 * configuration, that can only be set from php code.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
class stubDummyVariant extends stubAbstractVariant
{
    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return false;
    }

    /**
     * check whether the variant is valid
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isValid(stubSession $session, stubRequest $request)
    {
        return false;
    }
}
?>