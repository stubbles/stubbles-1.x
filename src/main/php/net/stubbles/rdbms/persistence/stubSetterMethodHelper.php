<?php
/**
 * Helper class to work with setter methods.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 * @version     $Id: stubSetterMethodHelper.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::types::stubDate',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn',
                      'net::stubbles::rdbms::persistence::stubPersistenceException'
);
/**
 * Helper class to work with setter methods.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence
 */
class stubSetterMethodHelper extends stubBaseObject
{
    /**
     * name of class to collect setter methods of
     *
     * @var  string
     */
    protected $className;
    /**
     * list of setter methods
     *
     * @var  array<string,array<string,mixed>>
     */
    protected $setterMethods = array();

    /**
     * constructor
     *
     * @param  string  $className  name of class to collect setter methods of
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * adds a setter method for given getter method
     *
     * @param   stubDatabaseTableColumn   $dbColumn          db column annotation from getter method
     * @param   string                    $getterMethodName  name of getter method
     * @throws  stubPersistenceException
     */
    public function addSetterMethod(stubDatabaseTableColumn $dbColumn, $getterMethodName)
    {
        $this->setterMethods[$dbColumn->getName()] = array('setterMethod' => self::getSetterMethodName($dbColumn, $this->className, $getterMethodName),
                                                           'type'         => $dbColumn->getType()
                                                     );
    }

    /**
     * applies the setter methods with given data on given entity
     *
     * @param   object  $entity  entity to apply setter methods on
     * @param   array   $data    data to apply setter methods with
     * @throws  stubPersistenceException
     */
    public function applySetterMethods($entity, array $data)
    {
        if (($entity instanceof $this->className) === false) {
            throw new stubPersistenceException('Given entity must be of type ' . $this->className);
        }
        
        foreach ($this->setterMethods as $columnName => $info) {
            if (isset($data[$columnName]) === false) {
                continue;
            }
            
            if ('DATETIME' === $info['type']) {
                $data[$columnName] = new stubDate($data[$columnName]);
            }
            
            $setterMethodName = $info['setterMethod'];
            $entity->$setterMethodName($data[$columnName]);
        }
    }

    /**
     * create setter method from given annotation for given class
     *
     * @param   stubDatabaseTableColumn   $dbColumn          description of column
     * @param   string                    $className         name of class which contains the method
     * @param   string                    $getterMethodName  name of the method annotated with DBColumn
     * @return  string                    name of setter setter method
     * @throws  stubPersistenceException
     */
    public static function getSetterMethodName(stubDatabaseTableColumn $dbColumn, $className, $getterMethodName)
    {
        $setterMethodName = (($dbColumn->hasSetterMethod() == true) ? ($dbColumn->getSetterMethod()) : (str_replace('get', 'set', $getterMethodName)));
        if (in_array($setterMethodName, get_class_methods($className)) === false) {
            throw new stubPersistenceException('Public setter method ' . $className . '::' . $setterMethodName . '() for database field ' . $dbColumn->getName() . '  does not exist.');
        }
        
        return $setterMethodName;
    }
}
?>