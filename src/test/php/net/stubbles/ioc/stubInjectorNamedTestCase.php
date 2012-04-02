<?php
/**
 * Test for net::stubbles::ioc::stubInjector with @Named annotation.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorNamedTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');
/**
 * Helper interface for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
interface stubInjectorNamedTestCase_Person
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello();
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedTestCase_Boss implements stubInjectorNamedTestCase_Person
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello()
    {
        return "boss";
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedTestCase_Employee implements stubInjectorNamedTestCase_Person
{
    /**
     * says hello
     *
     * @return  string
     */
    public function sayHello()
    {
        return "employee";
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedSetterInjectionTestCase_Developers
{
    public $mikey;
    public $schst;

    /**
     * Setter method with Named() annotation
     *
     * @param  Person  $schst
     * @Inject
     * @Named('schst')
     */
    public function setSchst(stubInjectorNamedTestCase_Person $schst)
    {
        $this->schst = $schst;
    }

    /**
     * Setter method without Named() annotation
     *
     * @param  Person  $schst
     * @Inject
     */
    public function setMikey(stubInjectorNamedTestCase_Person $mikey)
    {
        $this->mikey = $mikey;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParams
{
    public $mikey;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $boss
     * @param  stubInjectorNamedTestCase_Person  $employee
     * @Inject
     * @Named{boss}('schst')
     */
    public function setDevelopers(stubInjectorNamedTestCase_Person $boss, stubInjectorNamedTestCase_Person $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParams
{
    public $mikey;
    public $schst;

    /**
     * constructor with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $boss
     * @param  stubInjectorNamedTestCase_Person  $employee
     * @Inject
     * @Named{boss}('schst')
     */
    public function __construct(stubInjectorNamedTestCase_Person $boss, stubInjectorNamedTestCase_Person $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function setDevelopers(stubInjectorNamedTestCase_Person $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsWithConstant
{
    public $role;
    public $schst;

    /**
     * constructor method with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $schst
     * @param  string                            $role
     * @Inject
     * @Named{role}('boss')
     */
    public function __construct(stubInjectorNamedTestCase_Person $schst, $role)
    {
        $this->schst = $schst;
        $this->role  = $role;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsGroupedName
{
    public $mikey;
    public $schst;

    /**
     * setter method with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $schst
     * @param  stubInjectorNamedTestCase_Person  $employee
     * @Inject
     * @Named('schst')
     */
    public function setDevelopers(stubInjectorNamedTestCase_Person $boss, stubInjectorNamedTestCase_Person $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsGroupedName
{
    public $mikey;
    public $schst;

    /**
     * constructor method with Named() annotation on a specific param
     *
     * @param  stubInjectorNamedTestCase_Person  $schst
     * @param  stubInjectorNamedTestCase_Person  $employee
     * @Inject
     * @Named('schst')
     */
    public function __construct(stubInjectorNamedTestCase_Person $boss, stubInjectorNamedTestCase_Person $employee)
    {
        $this->schst = $boss;
        $this->mikey = $employee;
    }
}
/**
 * Test for net::stubbles::ioc::stubInjector with @Named annotation.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorNamedTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * name based setter injection with single param
     *
     * @test
     */
    public function namedSetterInjectionWithSingleParam()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorNamedTestCase_Person')->named('schst')->to('stubInjectorNamedTestCase_Boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedSetterInjectionTestCase_Developers');

        $this->assertInstanceOf('stubInjectorNamedSetterInjectionTestCase_Developers', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Employee', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorNamedTestCase_Person')->named('schst')->to('stubInjectorNamedTestCase_Boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParams');

        $this->assertInstanceOf('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParams', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Employee', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and one of them is name based
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedParam()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorNamedTestCase_Person')->named('schst')->to('stubInjectorNamedTestCase_Boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParams');

        $this->assertInstanceOf('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParams', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Employee', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->schst);
    }

    /**
     * name based setter injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new stubBinder();
        $binder->bindConstant()->named('boss')->to('role:boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsWithConstant');

        $this->assertInstanceOf('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsWithConstant', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Employee', $group->schst);
        $this->assertEquals('role:boss', $group->role);
    }

    /**
     * name based constructor injection with multiple params and one of them is a named constant
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndOneNamedConstantParam()
    {
        $binder = new stubBinder();
        $binder->bindConstant()->named('boss')->to('role:boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsWithConstant');

        $this->assertInstanceOf('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsWithConstant', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Employee', $group->schst);
        $this->assertEquals('role:boss', $group->role);
    }

    /**
     * name based setter injection with multiple params and both are named
     *
     * @test
     */
    public function namedSetterInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorNamedTestCase_Person')->named('schst')->to('stubInjectorNamedTestCase_Boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsGroupedName');

        $this->assertInstanceOf('stubInjectorNamedSetterInjectionTestCase_DevelopersMultipleParamsGroupedName', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->schst);
    }

    /**
     * name based constructor injection with multiple params and both are named
     *
     * @test
     */
    public function namedConstructorInjectionWithMultipleParamAndNamedParamGroup()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorNamedTestCase_Person')->named('schst')->to('stubInjectorNamedTestCase_Boss');
        $binder->bind('stubInjectorNamedTestCase_Person')->to('stubInjectorNamedTestCase_Employee');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person', 'schst'));
        $this->assertTrue($injector->hasBinding('stubInjectorNamedTestCase_Person'));

        $group = $injector->getInstance('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsGroupedName');

        $this->assertInstanceOf('stubInjectorNamedConstructorInjectionTestCase_DevelopersMultipleParamsGroupedName', $group);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->mikey);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Person', $group->schst);
        $this->assertInstanceOf('stubInjectorNamedTestCase_Boss', $group->schst);
    }
}
?>