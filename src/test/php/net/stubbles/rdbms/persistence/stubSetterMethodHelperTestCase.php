<?php
/**
 * Test for net::stubbles::rdbms::persistence::stubSetterMethodHelper.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @version     $Id: stubSetterMethodHelperTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::persistence::stubSetterMethodHelper',
                      'net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn'
);
require_once dirname(__FILE__) . '/../persistence/MockSinglePrimaryKeyEntity.php';
/**
 * Test for net::stubbles::rdbms::persistence::stubSetterMethodHelper.
 *
 * @package     stubbles
 * @subpackage  rdbms_persistence_test
 * @group       rdbms
 * @group       rdbms_persistence
 */
class stubSetterMethodHelperTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubSetterMethodHelper
     */
    protected $setterMethodHelper;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->setterMethodHelper = new stubSetterMethodHelper('MockSinglePrimaryKeyEntity');
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
        if ($dbColumnAnnotation->hasNullable() === true) {
            $column->setIsNullable($dbColumnAnnotation->isNullable());
        }

        return $column;
    }

    /**
     * @test
     */
    public function setterMethodsAreCalledWithValue()
    {
        $entity = new MockSinglePrimaryKeyEntity();
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'getId');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withAnnotation');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withDefaultValue');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withDate');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $this->setterMethodHelper->applySetterMethods($entity, array('id' => 909, 'default' => 'foo', 'ignored' => 'ignored', 'date' => null));
        $this->assertEquals(909, $entity->getId());
        $this->assertEquals('this is bar', $entity->withAnnotation());
        $this->assertEquals('foo', $entity->withDefaultValue());
        $this->assertNull($entity->withDate());
    }

    /**
     * @test
     */
    public function setterMethodsWithDate()
    {
        $entity = new MockSinglePrimaryKeyEntity();
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'getId');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withAnnotation');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withDefaultValue');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $refMethod   = new stubReflectionMethod('MockSinglePrimaryKeyEntity', 'withDate');
        $this->setterMethodHelper->addSetterMethod($this->createTableColumnFromAnnotation($refMethod->getAnnotation('DBColumn')),
                                                   $refMethod->getName()
        );
        $this->setterMethodHelper->applySetterMethods($entity, array('id' => 909, 'default' => 'foo', 'ignored' => 'ignored', 'date' => '2008-10-23 19:18:09'));
        $this->assertEquals(909, $entity->getId());
        $this->assertEquals('this is bar', $entity->withAnnotation());
        $this->assertEquals('foo', $entity->withDefaultValue());
        $this->assertEquals('2008-10-23 19:18:09', $entity->withDate()->format('Y-m-d H:i:s'));
    }

    /**
     * @test
     * @expectedException  stubPersistenceException
     */
    public function applySetterMethodsWithWrongEntityThrowsPersistenceException()
    {
        $this->setterMethodHelper->applySetterMethods(new stdClass(), array('id' => 909, 'default' => 'foo'));
    }
}
?>