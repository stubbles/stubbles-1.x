<?php
/**
 * Interface for database initializers.
 * 
 * @package     stubbles
 * @subpackage  rdbms
 * @version     $Id: stubDatabaseInitializer.php 3255 2011-12-02 12:26:00Z mikey $
 */
/**
 * Interface for database initializers.
 *
 * @package     stubbles
 * @subpackage  rdbms
 * @ImplementedBy(net::stubbles::rdbms::stubPropertyBasedDatabaseInitializer.class)
 */
interface stubDatabaseInitializer extends stubObject
{
    /**
     * sets the descriptor to be used
     *
     * @param   string                   $descriptor
     * @return  stubDatabaseInitializer
     */
    public function setDescriptor($descriptor);

    /**
     * checks whether connection data for given id exists
     *
     * @param   string  $id
     * @return  bool
     */
    public function hasConnectionData($id);

    /**
     * returns connection data with given id
     *
     * @param   string                      $id
     * @return  stubDatabaseConnectionData
     */
    public function getConnectionData($id);
}
?>