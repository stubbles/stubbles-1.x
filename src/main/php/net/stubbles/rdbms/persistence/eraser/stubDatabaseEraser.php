<?php
/**
 * Class for erasing the data of an entity object within a database.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser
 * @version     $Id: stubDatabaseEraser.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::criteria::stubCriterion',
                      'net::stubbles::rdbms::criteria::stubAndCriterion',
                      'net::stubbles::rdbms::criteria::stubEqualCriterion',
                      'net::stubbles::rdbms::persistence::stubPersistenceHelper',
                      'net::stubbles::rdbms::persistence::eraser::stubDatabaseEraserException'
);
/**
 * Class for erasing the data of an entity object within a database.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_eraser
 * @Singleton
 */
class stubDatabaseEraser extends stubPersistenceHelper
{
    /**
     * delete an entity from database by its primary keys
     *
     * @param   stubDatabaseConnection        $connection  connection to use for erasing the data
     * @param   stubObject                    $entity      entity to erase
     * @throws  stubIllegalArgumentException
     * @throws  stubDatabaseEraserException
     * @throws  stubPersistenceException
     */
    public function deleteByPrimaryKeys(stubDatabaseConnection $connection, stubObject $entity)
    {
        $entityClass =$entity->getClass();
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $table     = $this->getTableDescription($entityClass)->getName();
        $criterion = new stubAndCriterion();
        foreach ($this->getColumns($entityClass) as $method => $column) {
            if ($column->isPrimaryKey() === false) {
                continue;
            }
            
            $criterion->addCriterion(new stubEqualCriterion($column->getName(), $entity->$method(), $table));
        }
        
        if ($criterion->hasCriterion() === false) {
            throw new stubDatabaseEraserException('Can not delete instance of ' . $entityClass->getFullQualifiedClassName() . ' by its primary keys as it has no primary key.');
        }
        
        try {
            $result = $connection->query($this->databaseQueryBuilderProvider->create($connection)->createDelete($table, $criterion));
            $result->free();
        } catch (stubException $se) {
            throw new stubDatabaseEraserException('Can not delete instance of ' . $entityClass->getFullQualifiedClassName() . ' by its primary keys.', $se);
        }
    }

    /**
     * deletes all instances of an entity by given criterion
     *
     * @param   stubDatabaseConnection       $connection   connection to use for erasing the data
     * @param   stubCriterion                $criterion    the criterion that denotes all instances to delete
     * @param   stubBaseReflectionClass      $entityClass
     * @return  int                          amount of erased instances
     * @throws  stubPersistenceException
     * @throws  stubDatabaseEraserException
     */
    public function deleteByCriterion(stubDatabaseConnection $connection, stubCriterion $criterion, stubBaseReflectionClass $entityClass)
    {
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $table = $this->getTableDescription($entityClass)->getName();
        try {
            $result      = $connection->query($this->databaseQueryBuilderProvider->create($connection)->createDelete($table, $criterion));
            $deletedRows = $result->count();
            $result->free();
        } catch (stubDatabaseException $se) {
            throw new stubDatabaseEraserException('Can not delete any instance of ' . $entityClass->getFullQualifiedClassName() . ' by criterion ' . $criterion, $se);
        }
        
        return $deletedRows;
    }
}
?>