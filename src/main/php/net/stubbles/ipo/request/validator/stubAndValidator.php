<?php
/**
 * Class that combines differant validators that all have to be true in order
 * that this validator also reports true.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubAndValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubAbstractCompositeValidator');
/**
 * Class that combines differant validators that all have to be true in order
 * that this validator also reports true.
 * 
 * If any of the combined validators returns false the stubAndValidator
 * will return false as well.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubAndValidator extends stubAbstractCompositeValidator
{
    /**
     * validate the given value
     * 
     * If any of the validators returns false this will return false as well.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected function doValidate($value)
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($value) == false) {
                return false;
            }
        }
        
        return true;
    }
}
?>