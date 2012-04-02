<?php
/**
 * This is a mocked entity that has some annotations.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @version     $Id: MockSinglePrimaryKeyEntity.php 1907 2008-10-27 14:01:38Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate');
/**
 * This is a mocked entity that has some annotations.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @Entity(defaultOrder='bar ASC')
 * @DBTable(name='foo')
 */
class MockSinglePrimaryKeyEntity extends stubBaseObject
{
    /**
     * the default value
     *
     * @var  string
     */
    protected $defaultValue = null;
    /**
     * this is bar
     *
     * @var  string
     */
    protected $bar          = 'this is bar';
    /**
     * id of the entity
     *
     * @var  string
     */
    protected $id;
    /**
     * a date instance
     *
     * @var  stubDate
     */
    protected $date;

    /**
     * sets the id
     *
     * @param  string  $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * returns the primary key
     *
     * @return  string
     * @Id
     * @DBColumn(name='id', type='INT', size=10, isUnsigned=true)
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * method that has no annotation
     */
    public function noAnnotation() { }

    /**
     * sets bar
     *
     * @param  string  $bar
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * method that has a DBColumn annotation
     *
     * @DBColumn(name='bar', type='VARCHAR', size=10, isNullable=true, setterMethod='setBar')
     */
    public function withAnnotation()
    {
        return $this->bar;
    }

    /**
     * set the default value
     *
     * @param  string  $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * method that has a DBColumn annotation
     *
     * @DBColumn(name='default', type='VARCHAR', size=10, defaultValue='example', setterMethod='setDefaultValue')
     */
    public function withDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * sets the date instance
     *
     * @param  stubDate  $date
     */
    public function setDate(stubDate $date)
    {
        $this->date = $date;
    }

    /**
     * method that returns a date instance
     *
     * @return  stubDate
     * @DBColumn(name='date', type='DATETIME', setterMethod='setDate')
     */
    public function withDate()
    {
        return $this->date;
    }

    /**
     * a typical get-method, but with a param > should be ignored
     *
     * @param   mixed     $param
     * @return  stdClass
     */
    public function getSomethingWithParam($param)
    {
        return new stdClass();
    }
}
?>