<?php
/**
 * Test for net::stubbles::reflection::stubReflectionPackage.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @version     $Id: stubReflectionPackageTestCase.php 2976 2011-02-07 18:47:04Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::stubReflectionPackage');
/**
 * Test for net::stubbles::reflection::stubReflectionPackage.
 *
 * @package     stubbles
 * @subpackage  reflection_test
 * @group       reflection
 */
class stubReflectionPackageTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubReflectionPackage
     */
    protected $stubRefPackage;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection');
    }

    /**
     * @test
     */
    public function packagesWithSameNameAreEqual()
    {
        $stubRefPackage1 = new stubReflectionPackage('org::stubbles::test::reflection');
        $this->assertTrue($this->stubRefPackage->equals($stubRefPackage1));
        $this->assertTrue($stubRefPackage1->equals($this->stubRefPackage));
    }

    /**
     * @test
     */
    public function packagesWithDifferentNameAreNotEqual()
    {
        $stubRefPackage2 = new stubReflectionPackage('foo');
        $this->assertFalse($this->stubRefPackage->equals($stubRefPackage2));
        $this->assertFalse($stubRefPackage2->equals($this->stubRefPackage));
    }

    /**
     * @test
     */
    public function nonPackageInstancesAreNotEqual()
    {
        $this->assertFalse($this->stubRefPackage->equals(303));
    }

    /**
     * @test
     */
    public function stringRepresentationContainsClassInformations()
    {
        $this->assertEquals("net::stubbles::reflection::stubReflectionPackage[org::stubbles::test::reflection] {\n}\n",
                            (string) $this->stubRefPackage
        );
    }

    /**
     * @test
     */
    public function getNameReturnsPackageName()
    {
        $this->assertEquals('org::stubbles::test::reflection',
                            $this->stubRefPackage->getName()
        );
    }

    /**
     * @test
     */
    public function hasClassReturnsTrueForClassWhichIsInsidePackage()
    {
        $this->assertTrue($this->stubRefPackage->hasClass('OtherPackageClass'));
    }

    /**
     * @test
     */
    public function hasClassReturnsFalseForClassWhichIsNotInsidePackage()
    {
        $this->assertFalse($this->stubRefPackage->hasClass('NonExisting'));
    }

    /**
     * @test
     */
    public function getClassReturnsInstanceOfStubReflectionClass()
    {
        $refClass = $this->stubRefPackage->getClass('OtherPackageClass');
        $this->assertInstanceOf('stubReflectionClass', $refClass);
        $this->assertEquals('org::stubbles::test::reflection::OtherPackageClass',
                            $refClass->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function getClassesReturnsListOfClassesWithoutSubpackages()
    {
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package1');
        $this->assertEquals(2, count($stubRefPackage->getClasses()));
        
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package2');
        $this->assertEquals(0, count($stubRefPackage->getClasses()));
    }

    /**
     * @test
     */
    public function getClassesReturnsListOfClassesWithSubpackagesIfRecursionEnabled()
    {
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package1');
        $this->assertEquals(3, count($stubRefPackage->getClasses(true)));

        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package2');
        $this->assertEquals(0, count($stubRefPackage->getClasses(true)));
    }

    /**
     * @test
     */
    public function getClassNamesReturnsListOfClassNamesWithoutSubpackages()
    {
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package1');
        $this->assertEquals(array('org::stubbles::test::reflection::package1::Package1Class1',
                                  'org::stubbles::test::reflection::package1::Package1Class2'
                            ),
                            $stubRefPackage->getClassNames()
        );
        
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package2');
        $this->assertEquals(array(), $stubRefPackage->getClassNames());
    }

    /**
     * @test
     */
    public function getClassNamesReturnsListOfClassNamesWithSubpackagesIfRecursionEnabled()
    {
        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package1');
        $this->assertEquals(array('org::stubbles::test::reflection::package1::Package1Class1',
                                  'org::stubbles::test::reflection::package1::Package1Class2',
                                  'org::stubbles::test::reflection::package1::subpackage::SubPackage1Class1'
                            ),
                            $stubRefPackage->getClassNames(true)
        );

        $stubRefPackage = new stubReflectionPackage('org::stubbles::test::reflection::package2');
        $this->assertEquals(array(), $stubRefPackage->getClassNames(true));
    }

    /**
     * @test
     */
    public function hasPackageReturnsTrueIfSubpackageExists()
    {
        $this->assertTrue($this->stubRefPackage->hasPackage('package1'));
    }

    /**
     * @test
     */
    public function hasPackageReturnsFalseIfSubpackageDoesNotExist()
    {
        $this->assertFalse($this->stubRefPackage->hasPackage('nonexisting'));
    }

    /**
     * @test
     */
    public function getPackageReturnsInstanceForSubpackage()
    {
        $refPackage = $this->stubRefPackage->getPackage('package2');
        $this->assertInstanceOf('stubReflectionPackage', $refPackage);
        $this->assertEquals('org::stubbles::test::reflection::package2',
                            $refPackage->getName()
        );
    }

    /**
     * @test
     */
    public function getPackageNamesReturnsListOfPackageNamesWithoutSubpackages()
    {
        $this->assertEquals(array('package1'),
                            $this->stubRefPackage->getPackageNames()
        );
    }

    /**
     * @test
     */
    public function getPackageNamesReturnsListOfPackageNamesWithSubpackagesIfRecursionEnabled()
    {
        $this->assertEquals(array('package1', 'package1::subpackage'),
                            $this->stubRefPackage->getPackageNames(true)
        );
    }

    /**
     * @test
     */
    public function getPackageNamesReturnsListOfPackagesWithoutSubpackages()
    {
        $this->assertEquals(1, count($this->stubRefPackage->getPackages()));
    }

    /**
     * @test
     */
    public function getPackageNamesReturnsListOfPackagesWithSubpackagesIfRecursionEnabled()
    {
        $this->assertEquals(2, count($this->stubRefPackage->getPackages(true)));
    }
}
?>