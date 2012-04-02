<?php
/**
 * Base class with helper methods for entity operations.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @version     $Id: stubPersistenceHelper.php 3194 2011-10-11 12:21:42Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::stubPersistenceException',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilderProvider',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableDescription',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn',
                      'net::stubbles::reflection::reflection'
);
/**
 * Base class with helper methods for entity operations.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 */
abstract class stubPersistenceHelper extends stubBaseObject
{
    /**
     * query builder factory
     *
     * @var  stubDatabaseQueryBuilderProvider
     */
    protected $databaseQueryBuilderProvider;
    /**
     * list of table descriptions
     *
     * @var  array<string,stubDatabaseTableDescription>
     */
    protected static $tableDescriptions = array();
    /**
     * list of classes with their persistence data
     *
     * @var  array<string,array<string,stubDatabaseTableColumn>>
     */
    protected static $columns           = array();
    /**
     * list of forbidden methods
     *
     * @var  array<string>
     */
    protected $forbiddenMethods = array('getClass',
                                        'getPackage',
                                        'getClassName',
                                        'getPackageName',
                                        'getSerialized'
                                  );

    /**
     * constructor
     *
     * @param  stubDatabaseQueryBuilderFactory  $databaseQueryBuilderFactory
     * @Inject
     */
    public function __construct(stubDatabaseQueryBuilderProvider $databaseQueryBuilderProvider)
    {
        $this->databaseQueryBuilderProvider = $databaseQueryBuilderProvider;
    }

    /**
     * helper method to create the correct table definition
     *
     * @param   stubBaseReflectionClass       $entityClass
     * @return  stubDatabaseTableDescription
     */
    protected function getTableDescription(stubBaseReflectionClass $entityClass)
    {
        $fqClassName = $entityClass->getFullQualifiedClassName();
        if (isset(self::$tableDescriptions[$fqClassName]) === false) {
            if ($entityClass->hasAnnotation('DBTable') === true) {
                 self::$tableDescriptions[$fqClassName] = $this->createTableDescriptionFromAnnotation($entityClass->getAnnotation('DBTable'));
            } else {
                self::$tableDescriptions[$fqClassName] = new stubDatabaseTableDescription();
                self::$tableDescriptions[$fqClassName]->setName($entityClass->getName() . 's');
            }
        }
        
        return self::$tableDescriptions[$fqClassName];
    }

    /**
     * creates table description from annotation data
     *
     * @param   stubAnnotation                $dbTableAnnotation
     * @return  stubDatabaseTableDescription
     */
    protected function createTableDescriptionFromAnnotation(stubAnnotation $dbTableAnnotation)
    {
        return stubDatabaseTableDescription::create()
                                           ->setCharacterSet($dbTableAnnotation->getCharacterSet())
                                           ->setCollation($dbTableAnnotation->getCollation())
                                           ->setComment($dbTableAnnotation->getComment())
                                           ->setName($dbTableAnnotation->getName())
                                           ->setType($dbTableAnnotation->getType());
    }

    /**
     * returns list of columns and method names
     *
     * @param   stubBaseReflectionClass                $entityClass
     * @return  array<string,stubDatabaseTableColumn>
     */
    protected function getColumns(stubBaseReflectionClass $entityClass)
    {
        $fqClassName = $entityClass->getFullQualifiedClassName();
        if (isset(self::$columns[$fqClassName]) === false) {
            self::$columns[$fqClassName] = array();
            foreach ($entityClass->getMethods() as $method) {
                $column = $this->getTableColumn($method);
                if (null === $column) {
                    continue;
                }
                
                self::$columns[$fqClassName][$method->getName()] = $column;
            }
            
        }
        return self::$columns[$fqClassName];
    }

    /**
     * helper method to create the column definition
     * 
     * Returns null if the method does not return a proper definition.
     *
     * @param   stubReflectionMethod     $method
     * @return  stubDatabaseTableColumn
     * @throws  stubPersistenceException
     */
    private function getTableColumn(stubReflectionMethod $method)
    {
        if ($method->isStatic() === true || $method->isPublic() === false
                || in_array($method->getName(), $this->forbiddenMethods) === true
                || $method->hasAnnotation('Transient') === true
                || $method->getNumberOfParameters() > 0) {
            return null;
        }
        
        if ($method->hasAnnotation('DBColumn') === true) {
            $column = $this->createTableColumnFromAnnotation($method->getAnnotation('DBColumn'));
        } elseif (substr($method->getName(), 0, 3) !== 'get' && substr($method->getName(), 0, 2) !== 'is') {
            return null;
        } else {
            $column = new stubDatabaseTableColumn();
            $column->setName($this->getPropertyName($method->getName()));
            $returnType = $method->getReturnType();
            if (null === $returnType) {
                // no type hint or returns null -> ignore method
                return null;
            }
            
            if ($returnType instanceof stubReflectionClass) {
                if ($returnType->getName() !== 'stubDate') {
                    // not supported yet
                    throw new stubPersistenceException('Returning classes from entity getter methods is currently not supported, except for net::stubbles::lang::types::stubDate. Sorry. :(');
                }
                
                $column->setType('DATETIME');
            } else {
                switch ($returnType->value()) {
                    case 'int':
                        $column->setType('INT');
                        $column->setSize(10);
                        break;
                    
                    case 'float':
                        $column->setType('FLOAT');
                        $column->setSize(10);
                        break;
                    
                    case 'bool':
                        $column->setType('TINYINT');
                        $column->setSize(1);
                        break;
                    
                    default:
                        $column->setType('VARCHAR');
                        $column->setSize(255);
                }
            }
        }
        
        if ($method->hasAnnotation('Id') === true) {
            $column->setIsPrimaryKey(true);
        }
        
        return $column;
    }

    /**
     * creates column description from annotation data
     *
     * @param   stubAnnotation           $dbColumnAnnotation
     * @return  stubDatabaseTableColumn
     */
    protected function createTableColumnFromAnnotation(stubAnnotation $dbColumnAnnotation)
    {
        $column = stubDatabaseTableColumn::create()
                                         ->setCharacterSet($dbColumnAnnotation->getCharacterSet())
                                         ->setCollation($dbColumnAnnotation->getCollation())
                                         ->setDefaultValue($dbColumnAnnotation->getDefaultValue())
                                         ->setHasZerofill($dbColumnAnnotation->isZerofill())
                                         ->setIsKey($dbColumnAnnotation->isKey())
                                         ->setIsPrimaryKey($dbColumnAnnotation->isPrimaryKey())
                                         ->setIsUnique($dbColumnAnnotation->isUnique())
                                         ->setIsUnsigned($dbColumnAnnotation->isUnsigned())
                                         ->setName($dbColumnAnnotation->getName())
                                         ->setOrder($dbColumnAnnotation->getOrder())
                                         ->setSetterMethod($dbColumnAnnotation->getSetterMethod())
                                         ->setSize($dbColumnAnnotation->getSize())
                                         ->setType($dbColumnAnnotation->getType());
        if ($dbColumnAnnotation->hasIsNullable() === true) {
            $column->setIsNullable($dbColumnAnnotation->isNullable());
        }

        return $column;
    }

    /**
     * creates the property name from the name of the method
     *
     * @param   string  $methodName
     * @return  string
     */
    protected function getPropertyName($methodName)
    {
        $propertyName = str_replace('is', '', str_replace('get', '', $methodName));
        return strtolower($propertyName{0}) . substr($propertyName, 1);
    }
}
?>