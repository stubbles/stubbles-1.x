<?php
/**
 * This class gives you more power!
 * 
 * @package     stubbles
 * @subpackage  util
 * @version     $Id: Binford.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::interceptors::stubPostInterceptor',
                      'net::stubbles::ipo::request::filter::stubFilter',
                      'net::stubbles::ipo::request::validator::stubValidator',
                      'net::stubbles::ipo::response::stubResponse'
);
/**
 * This class gives you more power!
 * 
 * @package     stubbles
 * @subpackage  util
 * @see         http://binford.de/
 */
final class Binford extends stubBaseObject implements stubValidator, stubFilter, stubPostInterceptor
{
    /**
     * this is the power of this class
     */
    const POWER = 6100;
    
    /**
     * validate the given value
     *
     * @param   mixed  $value
     * @return  bool   true if value is ok, else false
     */
    public function validate($value)
    {
        if (self::POWER == $value || 'Binford' == $value || ('Binford ' . self::POWER) == $value) {
            return true;
        }
        
        return false;
    }
    
    /**
     * returns a list of criteria for the validator
     *
     * <code>
     * array('allowedValues' => array(6100, 'Binford', 'Binford 6100');
     * </code>
     * 
     * @return  array<string,mixed>  key is criterion name, value is criterion value
     */
    public function getCriteria()
    {
        return array('allowedValues' => array(self::POWER, 'Binford', 'Binford ' . self::POWER));
    }

    /**
     * execute the filter
     *
     * @param   mixed                $value  value to filter
     * @return  mixed                filtered value
     * @throws  stubFilterException  in case $value has errors
     */
    public function execute($value)
    {
        if ($this->validate($value) == true) {
            return $value;
        }
        
        return $this->getDefaultValue();
    }


    /**
     * returns a default value in case the value to filter is not set
     *
     * @return  mixed
     */
    public function getDefaultValue()
    {
        return 'Binford ' . self::POWER;
    }

    /**
     * does the postprocessing stuff
     *
     * @param  stubRequest   $request   access to request data
     * @param  stubSession   $session   access to session data
     * @param  stubResponse  $response  access to response data
     */
    public function postProcess(stubRequest $request, stubSession $session, stubResponse $response)
    {
        $response->addHeader('X-Binford', self::POWER);
    }
    
    /**
     * returns a unique hash code for the class
     *
     * @return  string
     */
    public function hashCode()
    {
        return self::POWER;
    }
    
    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return true;
        }
        
        return false;
    }
    
    /**
     * returns a string representation of the class
     * 
     * The result is a short but informative representation about the class and
     * its values.
     * <code>
     * net::stubbles::util::Binford {
     *     POWER(integer): 6100
     * }
     * </code>
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->getClassName() . " {\n    POWER(integer): " . self::POWER . "\n}\n";
    }
}
?>