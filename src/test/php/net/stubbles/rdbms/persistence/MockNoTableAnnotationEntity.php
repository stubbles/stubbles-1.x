<?php
/**
 * This is a mocked entity that is missing the @DBTable annotation.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 */
/**
 * This is a mocked entity that is missing the @DBTable annotation.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @Entity
 */
class MockNoTableAnnotationEntity extends stubBaseObject
{
    /**
     * id of the entity
     *
     * @var  string
     */
    protected $id;

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
     * method that has a no annotation
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * method that has a no annotation
     *
     * @return int
     */
    public function getIntValue()
    {
        return 313;
    }

    /**
     * setter method to prevent exceptions
     *
     * @param  int  $intValue
     */
    public function setIntValue($intValue)
    {
        // ignore this
    }

    /**
     * method that has a no annotation
     *
     * @return  bool
     */
    public function getBoolValue()
    {
        return true;
    }

    /**
     * setter method to prevent exceptions
     *
     * @param  bool  $boolValue
     */
    public function setBoolValue($boolValue)
    {
        // ignore this
    }

    /**
     * method that has a no annotation
     *
     * @return  float
     */
    public function getFloatValue()
    {
        return 3.13;
    }

    /**
     * setter method to prevent exceptions
     *
     * @param  float  $floatValue
     */
    public function setFloatValue($floatValue)
    {
        // ignore this
    }

    /**
     * this method is ignored
     *
     * @return  string
     * @Transient
     */
    public function getIgnored()
    {
        return 'ignored';
    }

    /**
     * this method is ignored because it has no return doc annotation
     */
    public function getIgnored2()
    {
        return 'ignored2';
    }
}
?>