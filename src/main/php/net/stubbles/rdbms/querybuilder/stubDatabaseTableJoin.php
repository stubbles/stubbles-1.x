<?php
/**
 * Class that contains the description for a database table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 * @version     $Id: stubDatabaseTableJoin.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException');
/**
 * Class that contains the description for a database table.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder
 */
class stubDatabaseTableJoin extends stubBaseObject
{
    /**
     * name of the table which is joined
     *
     * @var  string
     */
    protected $name          = '';
    /**
     * list of allowed join types
     *
     * @var  array<string>
     */
    protected static $types  = array('LEFT', 'RIGHT', 'INNER', 'OUTER', 'CROSS', 'STRAIGHT', 'NATURAL');
    /**
     * type of the table
     *
     * @var  string
     */
    protected $type          = '';
    /**
     * the condition type (ON|USING)
     *
     * @var  string
     */
    protected $conditionType = '';
    /**
     * the join condition
     *
     * @var  string
     */
    protected $condition     = null;

    /**
     * set the name of the table which is joined
     *
     * @param  string  $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * return the name of the table which is joined
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
     * @param   string  $type
     * @throws  stubIllegalArgumentException
     */
    public function setType($type)
    {
        $type  = strtoupper($type);
        $types = explode(' ', $type);
        foreach ($types as $typeTest) {
            if (in_array($typeTest, self::$types) === false) {
                throw new stubIllegalArgumentException('The given join type ' . $type . ' is not an allowed join type. Allowed join types are ' . join(', ', self::$types));
            }
        }

        $this->type = $type;
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
     * sets the condition type
     *
     * @param   string  $conditionType
     * @throws  stubIllegalArgumentException
     */
    public function setConditionType($conditionType)
    {
        $conditionTypes = array('ON', 'USING');
        $conditionType  = strtoupper($conditionType);
        if (in_array($conditionType, $conditionTypes) === false) {
            throw new stubIllegalArgumentException('The given join condition type ' . $conditionType . ' is not an allowed join type. Allowed join condition types are ' . join(', ', $conditionTypes));
        }

        $this->conditionType = $conditionType;
    }

    /**
     * returns the condition type
     *
     * @return  string
     */
    public function getConditionType()
    {
        return $this->conditionType;
    }

    /**
     * sets the join condition
     *
     * @param  string  $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * checks whether the join has a condition
     *
     * @return  string
     */
    public function hasCondition()
    {
        return (null != $this->condition);
    }

    /**
     * returns the join condition
     *
     * @return  string
     */
    public function getCondition()
    {
        return $this->condition;
    }
}
?>