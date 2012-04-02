<?php
/**
 * Class that combines differant validators where one has to be true.
 * 
 * @package     stubbles
 * @subpackage  ipo_request_validator
 * @version     $Id: stubXorValidator.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubAbstractCompositeValidator');
/**
 * Class that combines differant validators where one has to be true.
 * 
 * If no validator or more than one validator returns false the stubXorValidator
 * will return false as well. It only returns true if one validator returns true
 * and any other validator returns false.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator
 */
class stubXorValidator extends stubAbstractCompositeValidator
{
    /**
     * validate the given value
     * 
     * If no validator or more than one validator returns false it
     * will return false as well. It only returns true if one
     * validator returns true and any other validator returns false.
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    protected function doValidate($value)
    {
        $trueCount = 0;
        foreach ($this->validators as $validator) {
            if ($validator->validate($value) === true) {
                $trueCount++;
                if (1 < $trueCount) {
                    // more than one true received,
                    // can not return with true any more
                    return false;
                }
            }
        }
        
        return (1 == $trueCount);
    }
}
?>