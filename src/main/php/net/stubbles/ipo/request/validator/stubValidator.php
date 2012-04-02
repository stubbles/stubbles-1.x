<?php
/**
 * Interface for validators.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * Interface for validators.
 * 
 * Validators allow simple checks whether a value fulfils a set of criteria.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
interface stubValidator
{
    /**
     * validate the given value
     * 
     * Returns true if the value does fulfils all of the criteria, else false.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    public function validate($value);
    
    /**
     * returns a list of criteria for the validator
     *
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria();
}
?>