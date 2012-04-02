<?php
/**
 * Class for iterating over selected entities from a query result.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder
 * @version     $Id: stubDatabaseFinderResult.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::finder::stubDatabaseFinderException',
                      'net::stubbles::reflection::stubReflectionClass'
);
/**
 * Class for iterating over selected entities from a query result.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_finder
 */
class stubDatabaseFinderResult extends stubBaseObject implements Iterator
{
    /**
     * reflection class for entity
     *
     * @var  stubBaseReflectionClass
     */
    protected $entityClass;
    /**
     * the result set from the database query
     *
     * @var  ArrayIterator
     */
    protected $resultIterator;
    /**
     * list of setter methods for entity
     *
     * @var  array
     */
    protected $setterMethodHelper;

    /**
     * constructor
     *
     * @param  stubBaseReflectionClass  $entityClass         reflection class for entity
     * @param  array                    $result              result set from the database query
     * @param  stubSetterMethodHelper   $setterMethodHelper  list of setter methods for entity
     */
    public function __construct(stubBaseReflectionClass $entityClass, array $result, stubSetterMethodHelper $setterMethodHelper)
    {
        $this->entityClass        = $entityClass;
        $this->resultIterator     = new ArrayIterator($result);
        $this->setterMethodHelper = $setterMethodHelper;
    }

    /**
     * returns reflection class for entity
     *
     * @return  stubBaseReflectionClass
     */
    public function forClass()
    {
        return $this->entityClass;
    }

    /**
     * returns amount of found entities
     *
     * @return  int
     */
    public function count()
    {
        return $this->resultIterator->count();
    }

    /**
     * returns the current entity
     *
     * @return  object
     * @throws  stubDatabaseFinderException
     */
    public function current()
    {
        try {
            $entity = $this->entityClass->newInstance();
        } catch (ReflectionException $re) {
            throw new stubDatabaseFinderException('Can not create a new instance of ' . $this->entityClass->getFullQualifiedClassName(), $re);
        }
        
        if (null === $entity) {
            throw new stubDatabaseFinderException('Can not create a new instance of ' . $this->entityClass->getFullQualifiedClassName());
        }
        
        $this->setterMethodHelper->applySetterMethods($entity, $this->resultIterator->current());
        return $entity;
    }

    /**
     * returns the current key
     *
     * @return  int
     */
    public function key()
    {
        return $this->resultIterator->key();
    }

    /**
     * moves iterator to next element
     */
    public function next()
    {
        $this->resultIterator->next();
    }

    /**
     * resets iterator to first element
     */
    public function rewind()
    {
        $this->resultIterator->rewind();
    }

    /**
     * checks whether pointer is on a valid element
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->resultIterator->valid();
    }
}
?>