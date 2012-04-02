<?php
/**
 * Class for creating the table description for an entity.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_creator
 * @version     $Id: stubDatabaseCreator.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::persistence::stubPersistenceHelper',
                      'net::stubbles::rdbms::persistence::creator::stubDatabaseCreatorException'
);
/**
 * Class for creating the table description for an entity.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_creator
 * @Singleton
 */
class stubDatabaseCreator extends stubPersistenceHelper
{
    /**
     * creates the table description from the given entity class
     *
     * @param   stubDatabaseConnection        $connection   connection to use for creating the tables
     * @param   stubBaseReflectionClass       $entityClass  entity type to create table for
     * @throws  stubDatabaseCreatorException
     * @throws  stubPersistenceException
     */
    public function createTable(stubDatabaseConnection $connection, stubBaseReflectionClass $entityClass)
    {
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $tableDescription = $this->getTableDescription($entityClass);
        foreach ($this->getColumns($entityClass) as $column) {
            $tableDescription->addColumn($column);
        }
        
        try {
            $connection->query($this->databaseQueryBuilderProvider->create($connection)->createTable($tableDescription));
        } catch (stubException $se) {
            throw new stubDatabaseCreatorException('Can not create table for ' . $entityClass->getFullQualifiedClassName(), $se);
        }
    }
}
?>