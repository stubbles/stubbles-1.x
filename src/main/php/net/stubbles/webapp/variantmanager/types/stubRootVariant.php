<?php
/**
 * Root variant that contains all other variants.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubRootVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubAbstractVariant');
/**
 * Root variant that contains all other variants.
 * 
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
class stubRootVariant extends stubAbstractVariant
{
    /**
     * constructor
     */
    public function __construct()
    {
        $this->name = 'root';
    }
    
    /**
     * sets the name of the variant
     *
     * @param   string                   $name
     * @return  stubConfigurableVariant
     */
    public function setName($name)
    {
        // can not reset name of RootVariant
        return $this;
    }
    
    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return true;
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
        return true;
    }
}
?>