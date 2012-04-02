<?php
/**
 * Interface for composite validators.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubCompositeValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubValidator');
/**
 * Interface for composite validators.
 * 
 * Composite validators can be used to combine two or more validators
 * into a single validator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
interface stubCompositeValidator extends stubValidator
{
    /**
     * add a validator
     *
     * @param  stubValidator  $validator
     */
    public function addValidator(stubValidator $validator);
}
?>