<?php
/**
 * Service class to be used in tests.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 * @version     $Id: TestService.php 3230 2011-11-23 17:04:19Z mikey $
 */
/**
 * Service class to be used in tests.
 *
 * @package     stubbles
 * @subpackage  service_jsonrpc_subprocessors_test
 */
class TestService extends stubBaseObject
{
    /**
     * test method for web service
     *
     * @param   int  $a
     * @param   int  $b
     * @return  int
     * @WebMethod
     */
    public function add($a, $b)
    {
        return ($a + $b);
    }

    /**
     * test method for web service and date strings/object
     *
     * @param   stubDate  $date
     * @param   boolean   $asArray
     * @return  stubDate|array<stubDate>
     * @WebMethod
     */
    public function addOneDay(stubDate $date, $asArray = false)
    {
        return (($asArray === true)
                    ? array($date->change()->to("+1 day"))
                    : $date->change()->to("+1 day"));
    }

    /**
     * another method that is not marked as WebMethod
     *
     * @param   int  $a
     * @param   int  $b
     * @return  int
     */
    public function mod($a, $b)
    {
        return ($a % $b);
    }
}
?>