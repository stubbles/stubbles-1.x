<?php
/**
 * Class that combines differant validators where one has to be true.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubOrValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubAbstractCompositeValidator');
/**
 * Class that combines differant validators where one has to be true.
 * 
 * If any of the combined validators returns true the stubOrValidator
 * will return true as well.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubOrValidator extends stubAbstractCompositeValidator
{
    /**
     * validate the given value
     * 
     * If any of the validators returns true this will return true as well.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected function doValidate($value)
    {
        foreach ($this->validators as $validator) {
            if ($validator->validate($value) == true) {
                return true;
            }
        }
        
        return false;
    }
}
?>