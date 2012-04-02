<?php
/**
 * Class that contains the description for a database table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseTableDescription.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn',
                      'net::stubbles::rdbms::stubDatabaseException'
);
/**
 * Class that contains the description for a database table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
class stubDatabaseTableDescription extends stubBaseObject
{
    /**
     * name of the table
     *
     * @var  string
     */
    protected $name             = '';
    /**
     * type of the table
     *
     * @var  string
     */
    protected $type;
    /**
     * character set of the table
     *
     * @var  string
     */
    protected $characterSet;
    /**
     * collation of the table
     *
     * @var  string
     */
    protected $collation;
    /**
     * comment for the table
     *
     * @var  string
     */
    protected $comment;
    /**
     * columns of the table
     *
     * @var  array<int,stubDatabaseTableColumn>
     */
    protected $columns          = array();
    /**
     * columns of table sorted by name
     *
     * @var  array<string>
     */
    protected $columnNames      = array();
    /**
     * the column order counter
     *
     * @var  int
     */
    protected $columnOrder      = 1;

    /**
     * static constructor
     *
     * @return  stubDatabaseTableDescription
     */
    public static function create()
    {
        return new self();
    }

    /**
     * set the name of the table
     *
     * @param   string                        $name
     * @return  stubDatabaseTableDescription
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * return the name of the table
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * set the type of the table
     *
     * @param   string                        $type
     * @return  stubDatabaseTableDescription
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * check whether the table has a type
     *
     * @return  bool
     */
    public function hasType()
    {
        return (null != $this->type);
    }

    /**
     * get the type of the table
     *
     * @return  string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * set the character set of the table
     *
     * @param   string                        $characterSet
     * @return  stubDatabaseTableDescription
     */
    public function setCharacterSet($characterSet)
    {
        $this->characterSet = $characterSet;
        return $this;
    }

    /**
     * check whether the table has a character set
     *
     * @return  bool
     */
    public function hasCharacterSet()
    {
        return (null != $this->characterSet);
    }

    /**
     * return the character set of the table
     *
     * @return  string
     */
    public function getCharacterSet()
    {
        return $this->characterSet;
    }

    /**
     * set the collation of the table
     *
     * @param   string                        $collation
     * @return  stubDatabaseTableDescription
     */
    public function setCollation($collation)
    {
        $this->collation = $collation;
        return $this;
    }

    /**
     * check whether the table has a collation
     *
     * @return  bool
     */
    public function hasCollation()
    {
        return (null != $this->collation);
    }

    /**
     * return the collation of the table
     *
     * @return  string
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * set the comment for the table
     *
     * @param   string                        $comment
     * @return  stubDatabaseTableDescription
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * check whether the table has a comment
     *
     * @return  bool
     */
    public function hasComment()
    {
        return (null != $this->comment);
    }

    /**
     * return the comment for the table
     *
     * @return  string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * add a column to the table
     *
     * @param   stubDatabaseTableColumn  $column
     * @throws  stubDatabaseException
     */
    public function addColumn(stubDatabaseTableColumn $column)
    {
        if (in_array($column->getName(), $this->columnNames) == true) {
            throw new stubDatabaseException('The column ' . $column->getName() . ' already exists in table ' . $this->getName());
        }
        
        $this->columnNames[] = $column->getName();
        $order = $column->getOrder();
        if (0 == $order) {
            $order = $this->columnOrder;
            $this->columnOrder++;
        }
        
        if (isset($this->columns[$order]) == true) {
            throw new stubDatabaseException('Can not add column ' . $column->getName() . ' with order ' . $order . ', there is already column ' . $this->columns[$order]->getName() . ' at this place.');
        }
        
        $this->columns[$order] = $column;
    }

    /**
     * return the list of columns of the table
     *
     * @return  array<string,stubDatabaseTableColumn>
     */
    public function getColumns()
    {
        ksort($this->columns);
        return $this->columns;
    }
}
?>