<?php
/**
 * Test for net::stubbles::ioc::stubInjector.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorBasicTestCase.php 2924 2011-01-16 13:07:18Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');
/**
 * Helper interface for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
interface stubInjectorTestCase_Tire
{
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate();
}
/**
 * Helper class for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_Goodyear implements stubInjectorTestCase_Tire
{
    /**
     * rotates the tires
     *
     * @return  string
     */
    public function rotate()
    {
        return "I'm driving with Goodyear tires.";
    }
}
/**
 * Helper class to test implicit binding with concrete class names.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_ImplicitDependency
{
    /**
     * instance from constructor injection
     *
     * @var  stubInjectorTestCase_Goodyear
     */
    protected $goodyearByConstructor;
    /**
     * instance from setter injection
     *
     * @var  stubInjectorTestCase_Goodyear
     */
    protected $goodyearBySetter;

    /**
     * constructor
     *
     * @param  stubInjectorTestCase_Goodyear  $goodyear
     * @Inject
     */
    public function __construct(stubInjectorTestCase_Goodyear $goodyear)
    {
        $this->goodyearByConstructor = $goodyear;
    }

    /**
     * setter
     *
     * @param  stubInjectorTestCase_Goodyear  $goodyear
     * @Inject
     */
    public function setGoodyear(stubInjectorTestCase_Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from constructor injection
     *
     * @return  stubInjectorTestCase_Goodyear
     */
    public function getGoodyearByConstructor()
    {
        return $this->goodyearByConstructor;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  stubInjectorTestCase_Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Helper class to test implicit binding related to bug #102.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @link        http://stubbles.net/ticket/102
 */
class stubInjectorTestCase_ImplicitDependencyBug102
{
    /**
     * instance from setter injection
     *
     * @var  stubInjectorTestCase_Goodyear
     */
    protected $goodyearBySetter;

    /**
     * setter
     *
     * @param  stubInjectorTestCase_Goodyear  $goodyear
     * @Inject
     */
    public function setGoodyear(stubInjectorTestCase_Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  stubInjectorTestCase_Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Helper class to test implicit binding related to bug #102.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_ImplicitOptionalDependency
{
    /**
     * instance from setter injection
     *
     * @var  stubInjectorTestCase_Goodyear
     */
    protected $goodyearBySetter;

    /**
     * setter
     *
     * @param  stubInjectorTestCase_Goodyear  $goodyear
     * @Inject(optional=true)
     */
    public function setGoodyear(stubInjectorTestCase_Goodyear $goodyear)
    {
        $this->goodyearBySetter = $goodyear;
    }

    /**
     * returns the instance from setter injection
     *
     * @return  stubInjectorTestCase_Goodyear
     */
    public function getGoodyearBySetter()
    {
        return $this->goodyearBySetter;
    }
}
/**
 * Another helper interface for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
interface stubInjectorTestCase_Vehicle
{
    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward();
}
/**
 * Another helper class for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_Car implements stubInjectorTestCase_Vehicle
{
    /**
     * injected tire instance
     *
     * @var  stubInjectorTestCase_Tire
     */
    public $tire;

    /**
     * Create a new car
     *
     * @param  stubInjectorTestCase_Tire  $tire
     * @Inject
     */
    public function __construct(stubInjectorTestCase_Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}
/**
 * Another helper class for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_Bike implements stubInjectorTestCase_Vehicle
{
    /**
     * injected tire instance
     *
     * @var  stubInjectorTestCase_Tire
     */
    public $tire;

    /**
     * sets the tire
     *
     * @param  stubInjectorTestCase_Tire  $tire
     * @Inject
     */
    public function setTire(stubInjectorTestCase_Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}
/**
 * Another helper interface for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
interface stubInjectorTestCase_Roof
{
    /**
     * method to open the roof
     */
    public function open();
    /**
     * method to close the roof
     */
    public function close();
}
/**
 * Another helper class for injection and binding tests.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorTestCase_Convertible implements stubInjectorTestCase_Vehicle
{
    /**
     * injected tire instance
     *
     * @var  stubInjectorTestCase_Tire
     */
    public $tire;
    /**
     * injected roof instance
     *
     * @var   stubInjectorTestCase_Roof
     */
    public $roof;

    /**
     * sets the tire
     *
     * @param Tire $tire
     * @Inject
     */
    public function setTire(stubInjectorTestCase_Tire $tire)
    {
        $this->tire = $tire;
    }

    /**
     * sets the root
     *
     * @param  stubInjectorTestCase_Roof  $roof
     * @Inject(optional=true)
     */
    public function setRoof(stubInjectorTestCase_Roof $roof)
    {
        $this->roof = $roof;
    }

    /**
     * moves the vehicle forward
     *
     * @return  string
     */
    public function moveForward()
    {
        return $this->tire->rotate();
    }
}

/**
 * Test for net::stubbles::ioc::stubInjector.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorBasicTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test constructor injections
     *
     * @test
     */
    public function constructorInjection()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Tire')->to('stubInjectorTestCase_Goodyear');
        $binder->bind('stubInjectorTestCase_Vehicle')->to('stubInjectorTestCase_Car');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Vehicle'));
        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Tire'));

        $vehicle = $injector->getInstance('stubInjectorTestCase_Vehicle');

        $this->assertInstanceOf('stubInjectorTestCase_Vehicle', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Car', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Tire', $vehicle->tire);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function setterInjection()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Tire')->to('stubInjectorTestCase_Goodyear');
        $binder->bind('stubInjectorTestCase_Vehicle')->to('stubInjectorTestCase_Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Vehicle'));
        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Tire'));

        $vehicle = $injector->getInstance('stubInjectorTestCase_Vehicle');

        $this->assertInstanceOf('stubInjectorTestCase_Vehicle', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Bike', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Tire', $vehicle->tire);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $vehicle->tire);
    }

    /**
     * test setter injections while passing stubReflectionClass instances
     * instead of class names
     *
     * @test
     */
    public function setterInjectionWithClass()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Tire')->to(new stubReflectionClass('stubInjectorTestCase_Goodyear'));
        $binder->bind('stubInjectorTestCase_Vehicle')->to(new stubReflectionClass('stubInjectorTestCase_Bike'));

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Vehicle'));
        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Tire'));

        $vehicle = $injector->getInstance('stubInjectorTestCase_Vehicle');

        $this->assertInstanceOf('stubInjectorTestCase_Vehicle', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Bike', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Tire', $vehicle->tire);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $vehicle->tire);
    }

    /**
     * test bindings to an invalid type
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function setterInjectionWithInvalidArgument()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Vehicle')->to(313);
    }

    /**
     * test bindings to an instance
     *
     * @test
     */
    public function setterInjectionByInstance()
    {
        $tire = new stubInjectorTestCase_Goodyear();

        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Tire')->toInstance($tire);
        $binder->bind('stubInjectorTestCase_Vehicle')->to('stubInjectorTestCase_Bike');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Vehicle'));
        $this->assertTrue($injector->hasBinding('stubInjectorTestCase_Tire'));

        $vehicle = $injector->getInstance('stubInjectorTestCase_Vehicle');

        $this->assertInstanceOf('stubInjectorTestCase_Vehicle', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Bike', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Tire', $vehicle->tire);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $vehicle->tire);
        $this->identicalTo($vehicle->tire, $tire);
    }

    /**
     * test bindings to an instance with an invalid type
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function setterInjectionByInvalidInstance()
    {
        $tire = new stubInjectorTestCase_Goodyear();

        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Vehicle')->toInstance($tire);
    }

    /**
     * test setter injections
     *
     * @test
     */
    public function optionalSetterInjection()
    {
        $tire = new stubInjectorTestCase_Goodyear();

        $binder = new stubBinder();
        $binder->bind('stubInjectorTestCase_Tire')->to('stubInjectorTestCase_Goodyear');
        $binder->bind('stubInjectorTestCase_Vehicle')->to('stubInjectorTestCase_Convertible');

        $injector = $binder->getInjector();

        $vehicle = $injector->getInstance('stubInjectorTestCase_Vehicle');

        $this->assertInstanceOf('stubInjectorTestCase_Vehicle', $vehicle);
        $this->assertInstanceOf('stubInjectorTestCase_Convertible', $vehicle);

        $this->assertNull($vehicle->roof);
    }

    /**
     * test implicit bindings
     *
     * @test
     */
    public function implicitBinding()
    {
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $this->assertFalse($injector->hasExplicitBinding('stubInjectorTestCase_Goodyear'));
        $goodyear = $injector->getInstance('stubInjectorTestCase_Goodyear');
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $goodyear);
        $this->assertTrue($injector->hasExplicitBinding('stubInjectorTestCase_Goodyear'));
    }

    /**
     * test implicit bindings as a dependency
     *
     * @test
     */
    public function implicitBindingAsDependency()
    {
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $this->assertFalse($injector->hasExplicitBinding('stubInjectorTestCase_ImplicitDependency'));
        $obj      = $injector->getInstance('stubInjectorTestCase_ImplicitDependency');
        $this->assertInstanceOf('stubInjectorTestCase_ImplicitDependency', $obj);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $obj->getGoodyearByConstructor());
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $obj->getGoodyearBySetter());
        $this->assertTrue($injector->hasExplicitBinding('stubInjectorTestCase_ImplicitDependency'));
    }

    /**
     * test method for bug #102
     *
     * @link  http://stubbles.net/ticket/102
     *
     * @test
     * @group  bug102
     */
    public function bug102()
    {
        $obj      = new stubInjectorTestCase_ImplicitDependencyBug102();
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * optional implicit dependency will not be set
     *
     * @test
     */
    public function optionalImplicitDependencyWillNotBeSet()
    {
        $obj      = new stubInjectorTestCase_ImplicitOptionalDependency();
        $binder   = new stubBinder();
        $injector = $binder->getInjector();
        $injector->handleInjections($obj);
        $this->assertNull($obj->getGoodyearBySetter());
        
        $binder->bind('stubInjectorTestCase_Goodyear')->to('stubInjectorTestCase_Goodyear');
        $obj = new stubInjectorTestCase_ImplicitOptionalDependency();
        $injector->handleInjections($obj);
        $this->assertInstanceOf('stubInjectorTestCase_Goodyear', $obj->getGoodyearBySetter());
    }

    /**
     * given injector should be used instead of creating a new one
     *
     * @test
     */
    public function injectedInjectorIsUsed()
    {
        $injector = new stubInjector();
        $binder   = new stubBinder($injector);
        $this->assertSame($injector, $binder->getInjector());
    }

    /**
     * requesting a missing binding throws a binding exception
     *
     * @test
     * @expectedException  stubBindingException
     */
    public function missingBindingThrowsBindingException()
    {
        $injector = new stubInjector();
        $injector->getInstance('stubInjectorTestCase_Vehicle');
    }

    /**
     * requesting a missing binding throws a binding exception
     *
     * @test
     * @expectedException  stubBindingException
     */
    public function missingBindingOnInjectionHandlingThrowsBindingException()
    {
        $injector = new stubInjector();
        $class    = new stubInjectorTestCase_Bike();
        $injector->handleInjections($class);
    }

    /**
     * @test
     * @since  1.5.0
     */
    public function addBindingReturnsAddedBinding()
    {
        $injector    = new stubInjector();
        $mockBinding = $this->getMock('stubBinding');
        $this->assertSame($mockBinding, $injector->addBinding($mockBinding));
    }
}
?>