<?php
/**
 * Simple Math Service used for examples.
 *
 * @package     stubbles_examples
 * @subpackage  service
 * @version     $Id: MathService.php 3225 2011-11-23 16:09:41Z mikey $
 */
/**
 * Simple Math Service used for examples.
 *
 * @package     stubbles_examples
 * @subpackage  service
 */
class MathService
{
    /**
     * Add two numbers
     *
     * @WebMethod
     * @param   int     $arrKey
     * @return  string
     */
    public function add($a, $b)
    {
        return $a + $b;
    }

    /**
     * Method to throw an exception
     *
     * @WebMethod
     */
    public function throwException()
    {
        throw new stubException("This exception is intended.");
    }
}
?>