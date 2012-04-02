<?php
/**
 * Class for creating MySQL specific queries.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseMySQLQueryBuilder.php 3192 2011-10-11 09:01:50Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseQueryBuilder');
/**
 * Class for creating MySQL specific queries.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @see         http://mysql.com/
 * @Singleton
 */
class stubDatabaseMySQLQueryBuilder extends stubBaseObject implements stubDatabaseQueryBuilder
{
    /**
     * list of text types
     *
     * @var  array<string>
     * @see  http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
     */
    protected static $textTypes    = array('TINYTEXT',
                                           'MEDIUMTEXT',
                                           'TEXT',
                                           'LONGTEXT',
                                           'CHAR',
                                           'VARCHAR',
                                           'ENUM',
                                           'SET'
                                     );
    /**
     * list of numeric types
     *
     * @var  array<string>
     * @see  http://dev.mysql.com/doc/refman/5.0/en/numeric-type-overview.html
     */
    protected static $numericTypes = array('TINYINT',
                                           'SMALLINT',
                                           'MEDIUMINT',
                                           'INT',
                                           'INTEGER',
                                           'BIGINT',
                                           'FLOAT',
                                           'DOUBLE',
                                           'DOUBLE PRECISION',
                                           'REAL',
                                           'DECIMAL',
                                           'DEC',
                                           'NUMERIC',
                                           'FIXED'
                                     );
    /**
     * list of types that don't have a size
     *
     * @var  array<string>
     */
    protected static $noSizeTypes  = array('TEXT',
                                           'DATETIME',
                                           'DATE',
                                           'TIME',
                                           'TIMESTAMP',
                                           'YEAR'
                                     );

    /**
     * creates a select query
     *
     * @param   stubDatabaseSelect  $select
     * @return  string
     */
    public function createSelect(stubDatabaseSelect $select)
    {
        $selectQuery = 'SELECT * FROM `' . $select->getBaseTableName() . '`';
        if ($select->hasJoins() === true) {
            $joins = $select->getJoins();
            foreach ($joins as $join) {
                $selectQuery .= ' ' . $join->getType();
                $selectQuery .= (($join->getType() !== 'STRAIGHT') ? (' ') : ('_'));
                $selectQuery .= 'JOIN `' . $join->getName() . '`';
                if ($join->hasCondition() === true) {
                    $selectQuery .= ' ' . $join->getConditionType() . ' ';
                    if ($join->getConditionType() === 'USING') {
                        $selectQuery .= '(' . $join->getCondition() . ')';
                    } else {
                        $selectQuery .= $join->getCondition();
                    }
                }
            }
        }
        
        if ($select->hasCriterion() === true) {
            $selectQuery .= ' WHERE ' . $select->getCriterion()->toSQL();
        }
        
        if ($select->isOrdered() === true) {
            $selectQuery .= ' ORDER BY ' . $select->getOrderedBy();
        }
        
        if ($select->hasLimit() === true) {
            $selectQuery .= ' LIMIT ' . $select->getOffset() . ',' . $select->getAmount();
        }
        
        return $selectQuery;
    }

    /**
     * creates insert queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $tableRows
     * @return  array<string,string>
     * @throws  stubIllegalArgumentException
     */
    public function createInsert(array $tableRows)
    {
        $queries = array();
        foreach ($tableRows as $tableName => $tableRow) {
            if (($tableRow instanceof stubDatabaseTableRow) === false) {
                throw new stubIllegalArgumentException('Table row for table ' . $tableName . ' is not an instance of net::stubbles::querybuilder::stubDatabaseTableRow.');
            }
            
            $tableName = $tableRow->getTableName();
            $queries[$tableName] = 'INSERT INTO `' . $tableName . '` (`' . join('`, `', $tableRow->getColumnNames()) . '`) VALUES (';
            $counter = 0;
            foreach ($tableRow->getColumns() as $columnValue) {
                if (0 < $counter) {
                    $queries[$tableName] .= ', ';
                }
                
                if (null === $columnValue) {
                    $queries[$tableName] .= 'NULL';
                } elseif (is_int($columnValue) === true) {
                    $queries[$tableName] .= $columnValue;
                } else {
                    $queries[$tableName] .= "'" . addslashes($columnValue) . "'";
                }
                
                $counter++;
            }
            
            $queries[$tableName] .= ')';
        }
        
        return $queries;
    }

    /**
     * creates update queries from a serialized value
     *
     * @param   array<string,stubDatabaseTableRow>  $tableRows
     * @return  array<string,string>
     * @throws  stubIllegalArgumentException
     */
    public function createUpdate(array $tableRows)
    {
        $queries    = array();
        foreach ($tableRows as $tableName => $tableRow) {
            if (($tableRow instanceof stubDatabaseTableRow) === false) {
                throw new stubIllegalArgumentException('Table row for table ' . $tableName . ' is not an instance of net::stubbles::querybuilder::stubDatabaseTableRow.');
            }
            
            $tableName           = $tableRow->getTableName();
            $queries[$tableName] = 'UPDATE `' . $tableName . '` SET ';
            $counter             = 0;
            foreach ($tableRow->getColumns() as $columnName => $columnValue) {
                if (0 < $counter) {
                    $queries[$tableName] .= ', ';
                }
                
                $queries[$tableName] .= '`' . $columnName . '` = ';
                if (null === $columnValue) {
                    $queries[$tableName] .= 'NULL';
                } elseif (is_int($columnValue) === true) {
                    $queries[$tableName] .= $columnValue;
                } else {
                    $queries[$tableName] .= "'" . addslashes($columnValue) . "'";
                }
                
                $counter++;
            }
            
            if ($tableRow->hasCriterion() === true) {
                $queries[$tableName] .= ' WHERE ' . $tableRow->getCriterion()->toSQL();
            }
        }
        
        return $queries;
    }

    /**
     * creates a delete query
     *
     * @param   string         $table      the table to delete from
     * @param   stubCriterion  $criterion  the criterion to use for deletion
     * @return  string
     */
    public function createDelete($table, stubCriterion $criterion)
    {
        return 'DELETE FROM `' . $table . '` WHERE ' . $criterion->toSQL();
    }

    /**
     * creates the query to create a table for the given class
     *
     * @param   stubDatabaseTableDescription       $tableDescription
     * @return  string
     * @throws  stubDatabaseQueryBuilderException
     */
    public function createTable(stubDatabaseTableDescription $tableDescription)
    {
        $query   = 'CREATE TABLE `' . $tableDescription->getName() . "` (\n";
        $columns = $tableDescription->getColumns();
        if (count($columns) === 0) {
            throw new stubDatabaseQueryBuilderException('A table must contain at least one column, but description for table ' . $tableDescription->getName() . ' does not contain any column.');
        }
        
        $primaryKeys = array();
        $keys        = array();
        $uniques     = array();
        $counter     = 0;
        foreach ($columns as $column) {
            if (0 < $counter) {
                $query .= ",\n";
            }
            
            $counter++;
            $query   .= '  ' . $column->getName() . ' ' . strtoupper($column->getType());
            if (in_array(strtoupper($column->getType()), self::$noSizeTypes) === false) {
                $query   .= '(' . $column->getSize() . ')';
            }
            
            if ($this->isTextColumn($column->getType()) === true) {
                if ($column->hasCharacterSet() === true) {
                    $query .= ' CHARACTER SET ' . $column->getCharacterSet();
                }
                
                if ($column->hasCollation() === true) {
                    $query .= ' COLLATE ' . $column->getCollation();
                }
            } elseif ($this->isNumericColumn($column->getType()) === true) {
                if ($column->isUnsigned() == true) {
                    $query .= ' UNSIGNED';
                }
                
                if ($column->hasZerofill() == true) {
                    $query .= ' ZEROFILL';
                }
            }
            
            if ($column->isNullable() === true && $column->getDefaultValue() === null) {
                $query .= ' DEFAULT NULL';
            } else {
                if ($column->isNullable() === false) {
                    $query .= ' NOT NULL';
                }
                
                if ($column->getDefaultValue() !== null) {
                    $query .= " DEFAULT '" . $column->getDefaultValue() . "'";
                }
            }
            
            if ($column->isPrimaryKey() === true) {
                if ($this->isNumericColumn($column->getType()) === true) {
                    $query .= ' AUTO_INCREMENT';
                }
                
                $primaryKeys[] = $column->getName();
            } elseif ($column->isKey() === true) {
                $keys[] = $column->getName();
            } elseif ($column->isUnique() === true) {
                $uniques[] = $column->getName();
            }
        }
        if (count($primaryKeys) > 0) {
            if (count($primaryKeys) > 1) {
                $query = str_replace(' AUTO_INCREMENT', '', $query);
            }
            
            $query .= ",\n  PRIMARY KEY (`" . join('`, `', $primaryKeys) . '`)';
        }
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                $query .= ",\n  KEY (`" . $key . '`)';
            }
        }
        
        if (count($uniques) > 0) {
            foreach ($uniques as $unique) {
                $query .= ",\n  UNIQUE (`" . $unique . '`)';
            }
        }
        
        $query .= "\n)";
        if ($tableDescription->hasType() === true) {
            $query .= ' ENGINE = ' . $tableDescription->getType();
        }
        
        if ($tableDescription->hasCharacterSet() === true) {
            $query .= ' CHARACTER SET ' . $tableDescription->getCharacterSet();
        }
        
        if ($tableDescription->hasCollation() === true) {
            $query .= ' COLLATE ' . $tableDescription->getCollation();
        }
        
        if ($tableDescription->hasComment() === true) {
            $query .= " COMMENT = '" . $tableDescription->getComment() . "'";
        }
        
        return $query;
    }

    /**
     * checks whether the given column type is a text type
     *
     * @param   string  $columnType  the type to check
     * @return  bool
     * @see     http://dev.mysql.com/doc/refman/5.0/en/string-type-overview.html
     */
    protected function isTextColumn($columnType)
    {
        return in_array(strtoupper($columnType), self::$textTypes);
    }

    /**
     * check whether the given column type is a numeric type
     *
     * @param   string  $columnType  the type to check
     * @return  bool
     * @see     http://dev.mysql.com/doc/refman/5.0/en/numeric-type-overview.html
     */
    protected function isNumericColumn($columnType)
    {
        return in_array(strtoupper($columnType), self::$numericTypes);
    }
}
?>