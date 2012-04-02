<?php
/**
 * Class that contains the description for a database table column.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseTableColumn.php 2971 2011-02-07 18:24:48Z mikey $
 */
/**
 * Class that contains the description for a database table column.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
class stubDatabaseTableColumn extends stubBaseObject
{
    /**
     * order of column within table
     *
     * @var  int
     */
    protected $order        = 0;
    /**
     * name of the table
     *
     * @var  string
     */
    protected $name         = '';
    /**
     * type of the table
     *
     * @var  string
     */
    protected $type         = '';
    /**
     * size of the field
     *
     * @var  int|string
     */
    protected $size;
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
     * switch whether column is unsigned or not
     *
     * @var  bool
     */
    protected $isUnsigned   = false;
    /**
     * switch whether column values are zerofilled or not
     *
     * @var  bool
     */
    protected $hasZerofill  = false;
    /**
     * switch whether the column can be null or not
     *
     * @var  bool
     */
    protected $isNullable   = true;
    /**
     * default value of the column
     *
     * @var  mixed
     */
    protected $defaultValue = null;
    /**
     * switch whether column is a primary key or not
     *
     * @var  bool
     */
    protected $isPrimaryKey = false;
    /**
     * switch whether column is a key or not
     *
     * @var  bool
     */
    protected $isKey        = false;
    /**
     * switch whether column is unique or not
     *
     * @var  bool
     */
    protected $isUnique     = false;
    /**
     * the name of the setter method to use for restoring the value from database
     *
     * @var  string
     */
    protected $setterMethod = null;

    /**
     * static constructor
     *
     * @return  stubDatabaseTableColumn
     */
    public static function create()
    {
        return new self();
    }

    /**
     * set the order within the table
     *
     * @param   int                      $order
     * @return  stubDatabaseTableColumn
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * returns the order within the table
     *
     * @return  int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * set the name of the table
     *
     * @param   string                   $name
     * @return  stubDatabaseTableColumn
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
     * @param   string                   $type
     * @return  stubDatabaseTableColumn
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
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
     * sets the size of the column
     *
     * @param   int|string               $size
     * @return  stubDatabaseTableColumn
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * returns the size of the column
     *
     * @return  int|string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * set the character set of the table
     *
     * @param   string                   $characterSet
     * @return  stubDatabaseTableColumn
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
     * @param   string                   $collation
     * @return  stubDatabaseTableColumn
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
     * set whether the column may be null or not
     *
     * @param   bool                     $isUnsigned
     * @return  stubDatabaseTableColumn
     */
    public function setIsUnsigned($isUnsigned)
    {
        $this->isUnsigned = $isUnsigned;
        return $this;
    }

    /**
     * check whether the column may be null or not
     *
     * @return  bool
     */
    public function isUnsigned()
    {
        return (strstr(strtoupper($this->type), 'INT') == true && true == $this->isUnsigned);
    }

    /**
     * set whether the column values are zerofilled or not
     *
     * @param   bool                     $hasZerofill
     * @return  stubDatabaseTableColumn
     */
    public function setHasZerofill($hasZerofill)
    {
        $this->hasZerofill = $hasZerofill;
        return $this;
    }

    /**
     * check whether the column values are zerofilled or not
     *
     * @return  bool
     */
    public function hasZerofill()
    {
        return (strstr(strtoupper($this->type), 'INT') == true && true == $this->hasZerofill);
    }

    /**
     * set whether the column may be null or not
     *
     * @param   bool                     $isNullable
     * @return  stubDatabaseTableColumn
     */
    public function setIsNullable($isNullable)
    {
        $this->isNullable = $isNullable;
        return $this;
    }

    /**
     * check whether the column may be null or not
     *
     * @return  bool
     */
    public function isNullable()
    {
        return (false == $this->isPrimaryKey && true == $this->isNullable);
    }

    /**
     * sets the default value of the column
     *
     * @param   mixed                    $defaultValue
     * @return  stubDatabaseTableColumn
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * returns the default value of the column
     *
     * @return  mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * set whether the column is a primary key or not
     *
     * @param   bool                     $isPrimaryKey
     * @return  stubDatabaseTableColumn
     */
    public function setIsPrimaryKey($isPrimaryKey)
    {
        $this->isPrimaryKey = $isPrimaryKey;
        return $this;
    }

    /**
     * check whether the column is a primary key or not
     *
     * @return  bool
     */
    public function isPrimaryKey()
    {
        return $this->isPrimaryKey;
    }

    /**
     * set whether the column is a primary key or not
     *
     * @param   bool                     $isKey
     * @return  stubDatabaseTableColumn
     */
    public function setIsKey($isKey)
    {
        $this->isKey = $isKey;
        return $this;
    }

    /**
     * check whether the column is a primary key or not
     *
     * @return  bool
     */
    public function isKey()
    {
        return (false == $this->isPrimaryKey && true == $this->isKey);
    }

    /**
     * set whether the column is unique or not
     *
     * @param   bool                     $isUnique
     * @return  stubDatabaseTableColumn
     */
    public function setIsUnique($isUnique)
    {
        $this->isUnique = $isUnique;
        return $this;
    }

    /**
     * check whether the column is unique or not
     *
     * @return  bool
     */
    public function isUnique()
    {
        return (false == $this->isPrimaryKey && true == $this->isUnique);
    }

    /**
     * set the name of the setter method
     *
     * @param   string                   $setterMethod
     * @return  stubDatabaseTableColumn
     */
    public function setSetterMethod($setterMethod)
    {
        $this->setterMethod = $setterMethod;
        return $this;
    }

    /**
     * checks whether the name of the setter method is known
     *
     * @return  bool
     */
    public function hasSetterMethod()
    {
        return (null !== $this->setterMethod);
    }

    /**
     * returns the name of the setter method
     *
     * @return  string
     */
    public function getSetterMethod()
    {
        return $this->setterMethod;
    }
}
?>