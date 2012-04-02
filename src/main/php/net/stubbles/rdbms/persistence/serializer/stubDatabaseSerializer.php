<?php
/**
 * Serializer to store objects in database tables.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_serializer
 * @version     $Id: stubDatabaseSerializer.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate',
                      'net::stubbles::rdbms::stubDatabaseConnection',
                      'net::stubbles::rdbms::criteria::stubEqualCriterion',
                      'net::stubbles::rdbms::persistence::stubPersistenceHelper',
                      'net::stubbles::rdbms::persistence::stubSetterMethodHelper',
                      'net::stubbles::rdbms::persistence::serializer::stubDatabaseSerializerException',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableRow'
);
/**
 * Serializer to store objects in database tables.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_serializer
 * @Singleton
 */
class stubDatabaseSerializer extends stubPersistenceHelper
{
    /**
     * data has been inserted
     */
    const INSERT = 'insert';
    /**
     * data has been updated
     */
    const UPDATE = 'update';

    /**
     * takes an entity and inserts it into the database
     *
     * @param   stubDatabaseConnection           $connection  connection to use for finding the data
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubIllegalArgumentException
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function insert(stubDatabaseConnection $connection, stubObject $entity)
    {
        $entityClass = $entity->getClass();
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $stuff = $this->processEntity($entityClass, $entity, self::INSERT);
        try {
            $this->processInsertQueries($connection, $this->getInsertQuery($connection, $stuff['tableRow'], $entity, $stuff['defaultValues']), $entity, array_shift($stuff['primaryKeys']));
        } catch (stubDatabaseException $dbe) {
            throw new stubDatabaseSerializerException('Can not persist ' . $entityClass->getFullQualifiedClassName() . ': a database error occured.', $dbe);
        }
        
        return self::INSERT;
    }

    /**
     * takes an entity and updates its database entry
     *
     * @param   stubDatabaseConnection           $connection   connection to use for finding the data
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubIllegalArgumentException
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function update(stubDatabaseConnection $connection, stubObject $entity)
    {
        $entityClass = $entity->getClass();
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $stuff = $this->processEntity($entityClass, $entity, self::UPDATE);
        try {
            $this->processUpdateQueries($connection, $this->getUpdateQuery($connection, $stuff['tableRow'], $stuff['defaultValues']));
        } catch (stubDatabaseException $dbe) {
            throw new stubDatabaseSerializerException('Can not persist ' . $entityClass->getFullQualifiedClassName() . ': a database error occured.', $dbe);
        }
            
        return self::UPDATE;
    }

    /**
     * takes an entity and serializes it into the database
     *
     * @param   stubDatabaseConnection           $connection   connection to use for finding the data
     * @param   stubObject                       $entity
     * @return  string
     * @throws  stubIllegalArgumentException
     * @throws  stubDatabaseSerializerException
     * @throws  stubPersistenceException
     */
    public function serialize(stubDatabaseConnection $connection, stubObject $entity)
    {
        $entityClass = $entity->getClass();
        if ($entityClass->hasAnnotation('Entity') === false) {
            throw new stubPersistenceException('Class ' . $entityClass->getFullQualifiedClassName() . ' is not an entity.');
        }
        
        $stuff = $this->processEntity($entityClass, $entity);
        if (count($stuff['primaryKeys']) > 1) {
            throw new stubDatabaseSerializerException('Persistence error for ' . $entityClass->getFullQualifiedClassName() . ': only one primary key can be null, but at least two primary keys are null: ' . join(', ', array_keys($stuff['primaryKeys'])));
        }
        
        if ($stuff['tableRow']->hasCriterion() === true) {
            try {
                $this->processUpdateQueries($connection, $this->getUpdateQuery($connection, $stuff['tableRow'], $stuff['defaultValues']));
            } catch (stubDatabaseException $dbe) {
                throw new stubDatabaseSerializerException('Can not persist ' . $entityClass->getFullQualifiedClassName() . ': a database error occured.', $dbe);
            }
            
            return self::UPDATE;
        }
        
        try {
            $this->processInsertQueries($connection, $this->getInsertQuery($connection, $stuff['tableRow'], $entity, $stuff['defaultValues']), $entity, array_shift($stuff['primaryKeys']));
        } catch (stubDatabaseException $dbe) {
            throw new stubDatabaseSerializerException('Can not persist ' . $entityClass->getFullQualifiedClassName() . ': a database error occured.', $dbe);
        }
        
        return self::INSERT;
    }

    /**
     * processes the entity: create another presentation of data
     *
     * @param   stubBaseReflectionClass  $entityClass
     * @param   object                   $entity
     * @param   string                   $type         optional
     * @return  array
     * @throws  stubDatabaseSerializerException
     */
    protected function processEntity(stubBaseReflectionClass $entityClass, $entity, $type = null)
    {
        $tableRow      = new stubDatabaseTableRow($this->getTableDescription($entityClass)->getName());
        $primaryKeys   = array();
        $defaultValues = array();
        foreach ($this->getColumns($entityClass) as $method => $column) {
            $value = $entity->$method();
            if ($value instanceof stubDate) {
                $value = $value->format('Y-m-d H:i:s');
            }
            
            if ($column->isPrimaryKey() === true) {
                if (null === $value && self::UPDATE === $type) {
                    throw new stubDatabaseSerializerException('Persistence error for ' . $entityClass->getFullQualifiedClassName() . ': should be updated, but one primary key column is null: ' . $method);
                } elseif (null === $value) {
                    $primaryKeys[$method] = array('setterMethod' => stubSetterMethodHelper::getSetterMethodName($column, $entityClass->getName(), $method),
                                                  'tableName'    => $tableRow->getTableName()
                                            );
                } elseif (self::INSERT === $type) {
                    $tableRow->setColumn($column->getName(), $value);
                } else {
                    $tableRow->addCriterion(new stubEqualCriterion($column->getName(), $value, $tableRow->getTableName()));
                }
            } elseif (null === $value) {
                $defaultValue = $column->getDefaultValue();
                if ($column->isNullable() === false && null === $defaultValue) {
                    throw new stubDatabaseSerializerException('Persistence error for ' . $entityClass->getFullQualifiedClassName() . ': column ' . $column->getName() . ' is not allowed to be null but return value from method ' . $method . ' and default value are both null.');
                }
                
                $defaultValues[] = array('setterMethod' => stubSetterMethodHelper::getSetterMethodName($column, $entityClass->getName(), $method),
                                         'value'        => $value,
                                         'defaultValue' => $defaultValue,
                                         'column'       => $column->getName()
                                   );
            } else {
                $tableRow->setColumn($column->getName(), $value);
            }
        }
        
        return array('tableRow'      => $tableRow,
                     'defaultValues' => $defaultValues,
                     'primaryKeys'   => $primaryKeys
               );
    }

    /**
     * creates the queries required to process the insert
     *
     * @param   stubDatabaseConnection  $connection     connection to use for finding the data
     * @param   stubDatabaseTableRow    $tableRow
     * @param   stubObject              $entity
     * @param   array                   $defaultValues
     * @return  array<string>
     * @throws  stubDatabaseSerializerException
     */
    protected function getInsertQuery(stubDatabaseConnection $connection, stubDatabaseTableRow $tableRow, stubObject $entity, array $defaultValues)
    {
        $queryBuilder = $this->databaseQueryBuilderProvider->create($connection);
        try {
            // fill default values into entity and table row
            foreach ($defaultValues as $defaultValue) {
                // only reset entity with default value if default value is not null
                if (null !== $defaultValue['defaultValue']) {
                    $setterMethodName = $defaultValue['setterMethod'];
                    $entity->$setterMethodName($defaultValue['defaultValue']);
                }
                
                $tableRow->setColumn($defaultValue['column'], $defaultValue['defaultValue']);
            }

            return $queryBuilder->createInsert(array($tableRow->getTableName() => $tableRow));
        } catch (stubIllegalArgumentException $iae) {
            throw new stubDatabaseSerializerException('Creating the queries failed.', $iae);
        }
    }

    /**
     * creates the queries required to process the update
     *
     * @param   stubDatabaseConnection  $connection     connection to use for finding the data
     * @param   stubDatabaseTableRow    $tableRow
     * @param   array                   $defaultValues
     * @return  array<string>
     * @throws  stubDatabaseSerializerException
     */
    protected function getUpdateQuery(stubDatabaseConnection $connection, stubDatabaseTableRow $tableRow, array $defaultValues)
    {
        $queryBuilder = $this->databaseQueryBuilderProvider->create($connection);
        try {
            foreach ($defaultValues as $defaultValue) {
                $tableRow->setColumn($defaultValue['column'], $defaultValue['value']);
            }
            
            return $queryBuilder->createUpdate(array($tableRow->getTableName() => $tableRow));
        } catch (stubIllegalArgumentException $iae) {
            throw new stubDatabaseSerializerException('Creating the queries failed.', $iae);
        }
    }

    /**
     * process insert queries
     *
     * @param   stubDatabaseConnection  $connection        connection to use for finding the data
     * @param   array<string,string>    $queries           list of queries to process
     * @param   object                  $entity            the entity to process the queries for
     * @param   array<string,string>    $singlePrimaryKey  optional  information about the single primary key
     * @throws  stubDatabaseException
     */
    protected function processInsertQueries(stubDatabaseConnection $connection, array $queries, $entity, array $singlePrimaryKey = null)
    {
        foreach ($queries as $tableName => $query) {
            $connection->exec($query);
            if (null !== $singlePrimaryKey && $singlePrimaryKey['tableName'] == $tableName) {
                $setterMethodName = $singlePrimaryKey['setterMethod'];
                $entity->$setterMethodName($connection->getLastInsertId());
            }
        }
    }

    /**
     * process update queries
     *
     * @param   stubDatabaseConnection  $connection   connection to use for finding the data
     * @param   array<string,string>    $queries      list of queries to process
     * @throws  stubDatabaseException
     */
    protected function processUpdateQueries(stubDatabaseConnection $connection, array $queries)
    {
        foreach ($queries as $tableName => $query) {
            $connection->exec($query);
        }
    }
}
?>