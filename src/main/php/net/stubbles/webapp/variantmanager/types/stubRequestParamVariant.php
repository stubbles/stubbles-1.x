<?php
/**
 * RequestParamVariant
 * 
 * Will be triggered, if the request contains a specified parameter
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 * @version     $Id: stubRequestParamVariant.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubAbstractVariant');
/**
 * RequestParamVariant
 * 
 * Will be triggered, if the request contains a specified parameter
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_types
 */
class stubRequestParamVariant extends stubAbstractVariant
{
    /**
     * the name of the request parameter
     * 
     * @var  string
     */
    private $paramName  = null;
    /**
     * the value of the request parameter
     * 
     * @var  string
     */
    private $paramValue = null;
    
    /**
     * check whether the variant is an enforcing variant
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     * @throws  stubVariantConfigurationException
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        return $this->isValid($session, $request);
    }
    
    /**
     * check whether the variant is valid
     * 
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  bool
     * @throws  stubVariantConfigurationException
     */
    public function isValid(stubSession $session, stubRequest $request)
    {
        if (null == $this->paramName) {
            throw new stubVariantConfigurationException('RequestParamVariant requires the param name to be set.');
        }
        
        if ($request->hasParam($this->paramName) == false) {
            return false;
        }
        
        if (null == $this->paramValue) {
            return true;
        }
        
        return $request->validateParam($this->paramName)->isEqualTo($this->paramValue);
    }
    
    /**
     * Set the name of the request parameter
     * 
     * @param  string  $paramName  the paramName to set
     */
    public function setParamName($paramName)
    {
        $this->paramName = $paramName;
    }

    /**
     * Set the desired value of the request parameter
     * 
     * @param  string  $paramValue  the paramValue to set
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;
    }
}
?>