<?php
/**
 * Default implementation of an entity manager to hide the classes that do the real work.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @version     $Id: stubDefaultEntityManager.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::persistence::stubEntityManager',
                      'net::stubbles::rdbms::persistence::eraser::stubDatabaseEraser',
                      'net::stubbles::rdbms::persistence::finder::stubDatabaseFinder',
                      'net::stubbles::rdbms::persistence::serializer::stubDatabaseSerializer'
);
/**
 * Default implementation of an entity manager to hide the classes that do the real work.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 */
class stubDefaultEntityManager extends stubBaseObject implements stubEntityManager
{
    /**
     * connection instance to use
     *
     * @var  stubDatabaseConnection
     */
    protected $connection;
    /**
     * finder to be used for search requests
     *
     * @var  stubDatabaseFinder
     */
    protected $finder;
    /**
     * serializer to be used for storage requests
     *
     * @var  stubDatabaseSerializer
     */
    protected $serializer;
    /**
     * eraser to be used for deletion requests
     *
     * @var  stubDatabaseEraser
     */
    protected $eraser;

    /**
     * constructor
     *
     * @param  stubDatabaseConnection  $connection
     * @param  stubDatabaseFinder      $finder
     * @param  stubDatabaseSerializer  $serializer
     * @param  stubDatabaseEraser      $eraser
     * @Inject
     */
    public function __construct(stubDatabaseConnection $connection, stubDatabaseFinder $finder, stubDatabaseSerializer $serializer, stubDatabaseEraser $eraser)
    {
        $this->connection = $connection;
        $this->finder     = $finder;
        $this->serializer = $serializer;
        $this->eraser     = $eraser;
    }

    /**
     * get an entity from database by its primary keys
     *
     * @param   stubBaseReflectionClass      $entityClass  class information about the entity
     * @param   array                        $primaryKeys  list of primary keys (name => value)
     * @return  stubObject
     * @throws  stubDatabaseFinderException
     * @throws  stubPersistenceException
     */
    public function findByPrimaryKey(stubBaseReflectionClass $entityClass, array $primaryKeys)
    {
        return $this->finder->findByPrimaryKeys($this->connection, $entityClass, $primaryKeys);
    }

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
    public function findByCriterion(stubCriterion $criterion, stubBaseReflectionClass $entityClass, $orderBy = null, $offset = null, $amount = null)
    {
        return $this->finder->findByCriterion($this->connection, $criterion, $entityClass, $orderBy, $offset, $amount);
    }

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
    public function findAll(stubBaseReflectionClass $entityClass, $orderBy = null, $offset = null, $amount = null)
    {
        return $this->finder->findAll($this->connection, $entityClass, $orderBy, $offset, $amount);
    }

    /**
     * takes an entity and inserts it into the database
     *
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function insert(stubObject $entity)
    {
        return $this->serializer->insert($this->connection, $entity);
    }

    /**
     * takes an entity and updates the database entry
     *
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function update(stubObject $entity)
    {
        return $this->serializer->update($this->connection, $entity);
    }

    /**
     * takes an entity and serializes it into the database
     *
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function serialize(stubObject $entity)
    {
        return $this->serializer->serialize($this->connection, $entity);
    }

    /**
     * delete an entity from database by its primary keys
     *
     * @param   stubObject                   $entity
     * @throws  stubDatabaseEraserException
     * @throws  stubPersistenceException
     */
    public function deleteByPrimaryKeys(stubObject $entity)
    {
        $this->eraser->deleteByPrimaryKeys($this->connection, $entity);
    }

    /**
     * deletes all instances of an entity by given criterion
     *
     * @param   stubCriterion                $criterion    the criterion that denotes all instances to delete
     * @param   stubBaseReflectionClass      $entityClass
     * @return  int                          amount of erased instances
     * @throws  stubDatabaseEraserException
     */
    public function deleteByCriterion(stubCriterion $criterion, stubBaseReflectionClass $entityClass)
    {
        return $this->eraser->deleteByCriterion($this->connection, $criterion, $entityClass);
    }
}
?>