<?php
/**
 * Interface for factories creating stubRequestValueErrors.
 * 
 * @package     stubbles
 * @subpackage  ipo_request
 * @version     $Id: stubRequestValueErrorFactory.php 2653 2010-08-18 17:14:18Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueError');
/**
 * Interface for factories creating stubRequestValueErrors.
 *
 * @package     stubbles
 * @subpackage  ipo_request
 * @ImplementedBy(net::stubbles::ipo::request::stubRequestValueErrorPropertiesFactory.class)
 */
interface stubRequestValueErrorFactory
{
    /**
     * creates the  RequestValueError with the id from the given source
     *
     * @param   string                 $id      id of RequestValueError to create
     * @return  stubRequestValueError
     */
    public function create($id);
}
?>