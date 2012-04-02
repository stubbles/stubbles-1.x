<?php
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @version     $Id: stubDatabaseTableColumnTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn');
/**
 * Test for net::stubbles::rdbms::querybuilder::stubDatabaseTableColumn.
 *
 * @package     stubbles
 * @subpackage  rdbms_querybuilder_test
 * @group       rdbms
 * @group       rdbms_querybuilder
 */
class stubDatabaseTableColumnTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDatabaseTableColumn
     */
    protected $tableColumn;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->tableColumn = stubDatabaseTableColumn::create();
    }

    /**
     * @test
     */
    public function hasOrderPosition0ByDefault()
    {
        $this->assertEquals(0, $this->tableColumn->getOrder());
    }

    /**
     * @test
     */
    public function orderCanBeSet()
    {
        $this->assertEquals(5,
                            $this->tableColumn->setOrder(5)
                                              ->getOrder()
        );
    }

    /**
     * @test
     */
    public function nameIsEmptyBeDefault()
    {
        $this->assertEquals('', $this->tableColumn->getName());
    }

    /**
     * @test
     */
    public function nameCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableColumn->setName('foo')
                                              ->getName()
        );
    }

    /**
     * @test
     */
    public function typeIsEmptyBeDefault()
    {
        $this->assertEquals('', $this->tableColumn->getType());
    }

    /**
     * @test
     */
    public function typeCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableColumn->setType('foo')
                                              ->getType()
        );
    }

    /**
     * @test
     */
    public function sizeIsNullByDefault()
    {
        $this->assertNull($this->tableColumn->getSize());
    }

    /**
     * @test
     */
    public function sizeCanBeEnumeration()
    {
        $this->assertEquals("'1','2','3'",
                            $this->tableColumn->setSize("'1','2','3'")
                                              ->getSize()
        );
    }

    /**
     * @test
     */
    public function sizeCanBeInteger()
    {
        $this->assertEquals(6,
                            $this->tableColumn->setSize(6)
                                              ->getSize()
        );
    }

    /**
     * @test
     */
    public function hasNoCharacterSetByDefault()
    {
        $this->assertNull($this->tableColumn->getCharacterSet());
        $this->assertFalse($this->tableColumn->hasCharacterSet());
    }

    /**
      * @test
     */
    public function characterSetCanBeSet()
    {
        $this->assertEquals('utf-8',
                            $this->tableColumn->setCharacterSet('utf-8')
                                              ->getCharacterSet()
        );
        $this->assertTrue($this->tableColumn->hasCharacterSet());
    }

    /**
     * @test
     */
    public function hasNoCollationByDefault()
    {
        $this->assertNull($this->tableColumn->getCollation());
        $this->assertFalse($this->tableColumn->hasCollation());
    }

    /**
     * @test
     */
    public function collationCanBeSet()
    {
        $this->assertEquals('utf-8',
                            $this->tableColumn->setCollation('utf-8')
                                              ->getCollation()
        );
        $this->assertTrue($this->tableColumn->hasCollation());
    }

    /**
     * @test
     */
    public function isNotUnsignedByDefault()
    {
        $this->assertFalse($this->tableColumn->isUnsigned());
    }

    /**
     * @test
     */
    public function canNotEnableUnsignedWithoutType()
    {
        $this->assertFalse($this->tableColumn->setIsUnsigned(true)->isUnsigned());
    }

    /**
     * @test
     */
    public function canEnableUnsignedForTypeSmallInt()
    {
        $this->assertTrue($this->tableColumn->setType('smallint')
                                            ->setIsUnsigned(true)
                                            ->isUnsigned()
        );
    }

    /**
     * @test
     */
    public function canEnableUnsignedForTypeMediumInt()
    {
        $this->assertTrue($this->tableColumn->setType('mediumint')
                                            ->setIsUnsigned(true)
                                            ->isUnsigned()
        );
    }

    /**
     * @test
     */
    public function canEnableUnsignedForTypeInt()
    {
        $this->assertTrue($this->tableColumn->setType('int')
                                            ->setIsUnsigned(true)
                                            ->isUnsigned()
        );
    }

    /**
     * @test
     */
    public function canEnableUnsignedForTypeBigInt()
    {
         $this->assertTrue($this->tableColumn->setType('bigint')
                                             ->setIsUnsigned(true)
                                             ->isUnsigned());
    }

    /**
     * @test
     */
    public function canNotEnableUnsignedForTypeText()
    {
        $this->assertFalse($this->tableColumn->setType('text')
                                             ->setIsUnsigned(true)
                                             ->isUnsigned()
        );
    }

    /**
     * @test
     */
    public function zerofillIsDisabledByDefault()
    {
        $this->assertFalse($this->tableColumn->hasZerofill());
    }

    /**
     * @test
     */
    public function zerofillCanNotBeEnabledWithoutType()
    {
        $this->assertFalse($this->tableColumn->setHasZerofill(true)->hasZerofill());
    }

    /**
     * @test
     */
    public function zerofillCanBeEnabledForSmallInt()
    {
        $this->assertTrue($this->tableColumn->setType('smallint')
                                            ->setHasZerofill(true)
                                            ->hasZerofill()
        );
    }

    /**
     * @test
     */
    public function zerofillCanBeEnabledForMediumInt()
    {
        $this->assertTrue($this->tableColumn->setType('mediumint')
                                            ->setHasZerofill(true)
                                            ->hasZerofill()
        );
    }

    /**
     * @test
     */
    public function zerofillCanBeEnabledForInt()
    {
        $this->assertTrue($this->tableColumn->setType('int')
                                            ->setHasZerofill(true)
                                            ->hasZerofill()
        );
    }

    /**
     * @test
     */
    public function zerofillCanBeEnabledForBigInt()
    {
        $this->assertTrue($this->tableColumn->setType('bigint')
                                            ->setHasZerofill(true)
                                            ->hasZerofill()
        );
    }

    /**
     * @test
     */
    public function zerofillCanNotBeEnabledForText()
    {
        $this->assertFalse($this->tableColumn->setType('text')
                                             ->setHasZerofill(true)
                                             ->hasZerofill()
        );
    }

    /**
     * @test
     */
    public function isNullableByDefault()
    {
        $this->assertTrue($this->tableColumn->isNullable());
    }

    /**
     * @test
     */
    public function nullableCanBeDisabled()
    {
        $this->assertFalse($this->tableColumn->setIsNullable(false)->isNullable());
    }

    /**
     * @test
     */
    public function isNullableIsDisabledIfPrimaryKeyIsEnabled()
    {
        $this->assertFalse($this->tableColumn->setIsNullable(true)
                                             ->setIsPrimaryKey(true)
                                             ->isNullable()
        );
    }

    /**
     * @test
     */
    public function defaultValueIsNullByDefault()
    {
        $this->assertNull($this->tableColumn->getDefaultValue());
    }

    /**
     * @test
     */
    public function defaultValueCanBeSet()
    {
        $this->assertEquals('foo',
                            $this->tableColumn->setDefaultValue('foo')
                                              ->getDefaultValue()
        );
    }

    /**
     * @test
     */
    public function isNotPrimaryKeyByDefault()
    {
        $this->assertFalse($this->tableColumn->isPrimaryKey());
    }

    /**
     * @test
     */
    public function isPrimaryKeyCanBeEnabled()
    {
        $this->assertTrue($this->tableColumn->setIsPrimaryKey(true)
                                            ->isPrimaryKey()
        );
    }

    /**
     * @test
     */
    public function isNotKeyByDefault()
    {
        $this->assertFalse($this->tableColumn->isKey());
    }

    /**
     * @test
     */
    public function isKeyCanBeEnabled()
    {
        $this->assertTrue($this->tableColumn->setIsKey(true)->isKey());
    }

    /**
     * @test
     */
    public function isKeyIsDisabledIfPrimayKeyEnabled()
    {
        $this->assertFalse($this->tableColumn->setIsKey(true)
                                             ->setIsPrimaryKey(true)
                                             ->isKey()
        );
    }

    /**
     * @test
     */
    public function isNotUniqueByDefault()
    {
        $this->assertFalse($this->tableColumn->isUnique());
    }

    /**
     * @test
     */
    public function isUniqueCanBeEnabled()
    {
        $this->tableColumn->setIsUnique(true);
        $this->assertTrue($this->tableColumn->isUnique());
    }

    /**
     * @test
     */
    public function isUniqueIsDisabledIfPrimaryKeyEnabled()
    {
        $this->assertFalse($this->tableColumn->setIsUnique(true)
                                             ->setIsPrimaryKey(true)
                                             ->isUnique()
        );
    }

    /**
     * @test
     */
    public function hasNoSetterMethodByDefault()
    {
        $this->assertNull($this->tableColumn->getSetterMethod());
        $this->assertFalse($this->tableColumn->hasSetterMethod());
    }

    /**
     * @test
     */
    public function setterMethod()
    {
        $this->assertEquals('setFoo',
                            $this->tableColumn->setSetterMethod('setFoo')
                                              ->getSetterMethod()
        );
        $this->assertTrue($this->tableColumn->hasSetterMethod());
    }
}
?>