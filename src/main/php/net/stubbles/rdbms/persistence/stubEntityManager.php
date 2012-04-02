<?php
/**
 * Interface for an entity manager to hide the classes that do the real work.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @version     $Id: stubEntityManager.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::criteria::stubCriterion',
                      'net::stubbles::reflection::stubBaseReflectionClass'
);
/**
 * Interface for an entity manager to hide the classes that do the real work.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @ImplementedBy(net::stubbles::rdbms::persistence::stubDefaultEntityManager.class)
 */
interface stubEntityManager extends stubObject
{
    /**
     * get an entity from database by its primary keys
     *
     * @param   stubBaseReflectionClass      $entityClass  class information about the entity
     * @param   array                        $primaryKeys  list of primary keys (name => value)
     * @return  stubObject
     * @throws  stubDatabaseFinderException
     * @throws  stubPersistenceException
     */
    public function findByPrimaryKey(stubBaseReflectionClass $entityClass, array $primaryKeys);

    /**
     * finds all instances of $entityClass by given criterion
     *
     * @param   stubCriterion                $criterion
     * @param   string                       $entityClass  entity class to find instances of
     * @param   string                       $orderBy      optional  overrule default order of entity
     * @param   int                          $offset       optional  overrule to start selection at given offset
     * @param   int                          $amount       optional  overrule to limit selection to given amount
     * @return  stubDatabaseFinderResult     list of instances of $entityClass found with $criterion
     * @throws  stubDatabaseFinderException
     * @throws  stubPersistenceException
     */
    public function findByCriterion(stubCriterion $criterion, stubBaseReflectionClass $entityClass, $orderBy = null, $offset = null, $amount = null);

    /**
     * finds all instances of $entityClass
     *
     * @param   string                       $entityClass  entity class to find instances of
     * @param   string                       $orderBy      optional  overrule default order of entity
     * @param   int                          $offset       optional  overrule to start selection at given offset
     * @param   int                          $amount       optional  overrule to limit selection to given amount
     * @return  stubDatabaseFinderResult     list of instances of $entityClass found
     * @throws  stubDatabaseFinderException
     * @throws  stubPersistenceException
     */
    public function findAll(stubBaseReflectionClass $entityClass, $orderBy = null, $offset = null, $amount = null);

    /**
     * takes an entity and inserts it into the database
     *
     * @param   stubObject                        $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function insert(stubObject $entity);

    /**
     * takes an entity and updates the database entry
     *
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function update(stubObject $entity);

    /**
     * takes an entity and serializes it into the database
     *
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function serialize(stubObject $entity);

    /**
     * delete an entity from database by its primary keys
     *
     * @param   stubObject                   $entity
     * @throws  stubDatabaseEraserException
     * @throws  stubPersistenceException
     */
    public function deleteByPrimaryKeys(stubObject $entity);

    /**
     * deletes all instances of an entity by given criterion
     *
     * @param   stubCriterion                $criterion    the criterion that denotes all instances to delete
     * @param   stubBaseReflectionClass      $entityClass
     * @return  int                          amount of erased instances
     * @throws  stubDatabaseEraserException
     */
    public function deleteByCriterion(stubCriterion $criterion, stubBaseReflectionClass $entityClass);
}
?>