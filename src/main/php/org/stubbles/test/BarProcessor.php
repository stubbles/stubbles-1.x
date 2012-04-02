<?php
/**
 * A processor used in tests.
 *
 * @package     stubbles
 * @subpackage  test
 * @version     $Id: BarProcessor.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::websites::processors::stubAbstractProcessor');
/**
 * A processor used in tests.
 *
 * @package     stubbles
 * @subpackage  test
 */
class BarProcessor extends stubAbstractProcessor
{
    /**
     * returns the name of the current route
     *
     * @return  string
     */
    public function getRouteName()
    {
        return null;
    }

    /**
     * does the real processing
     */
    public function process()
    {
        // nothing to process here
    }

    public function getRequest()
    {
        return $this->request;
    }
    
    public function getSession()
    {
        return $this->session;
    }
}
?>