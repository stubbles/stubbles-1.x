<?php
/**
 * Test for net::stubbles::ioc::stubInjector with the singleton scope.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorSingletonTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');

interface stubInjectorSingletonTestCase_Number {
    public function display();
}

class stubInjectorSingletonTestCase_Random implements stubInjectorSingletonTestCase_Number {

    private $number;

    public function __construct() {
        srand();
        $this->number = rand(0, 5000);
    }

    public function display() {
        echo $this->number . "\n";
    }
}

/**
 * Class that is marked as Singleton
 *
 * @Singleton
 */
class stubInjectorSingletonTestCase_RandomSingleton implements stubInjectorSingletonTestCase_Number {

    private $number;

    public function __construct() {
        srand();
        $this->number = rand(0, 5000);
    }

    public function display() {
        echo $this->number . "\n";
    }
}

class stubInjectorSingletonTestCase_SlotMachine {

    public $number1;
    public $number2;

    /**
     * Set number 1
     *
     * @param Number $number
     * @Inject
     */
    public function setNumber1(stubInjectorSingletonTestCase_Number $number) {
        $this->number1 = $number;
    }

    /**
     * Set number 2
     *
     * @param Number $number
     * @Inject
     */
    public function setNumber2(stubInjectorSingletonTestCase_Number $number) {
        $this->number2 = $number;
    }
}


/**
 * Test for net::stubbles::ioc::stubInjector with the singleton scope.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorSingletonTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Test using the SingletonScope
     *
     * @test
     */
    public function withScope()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorSingletonTestCase_Number')
               ->to('stubInjectorSingletonTestCase_Random')
               ->asSingleton();

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorSingletonTestCase_Number'));

        $slot = $injector->getInstance('stubInjectorSingletonTestCase_SlotMachine');

        $this->assertInstanceOf('stubInjectorSingletonTestCase_SlotMachine', $slot);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Number', $slot->number1);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Random', $slot->number1);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Number', $slot->number2);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Random', $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
    }

    /**
     * Test the Singleton annotation
     *
     * @test
     */
    public function withAnnotation()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorSingletonTestCase_Number')
               ->to('stubInjectorSingletonTestCase_RandomSingleton');

        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('stubInjectorSingletonTestCase_Number'));

        $slot = $injector->getInstance('stubInjectorSingletonTestCase_SlotMachine');

        $this->assertInstanceOf('stubInjectorSingletonTestCase_SlotMachine', $slot);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Number', $slot->number1);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_RandomSingleton', $slot->number1);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_Number', $slot->number2);
        $this->assertInstanceOf('stubInjectorSingletonTestCase_RandomSingleton', $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
    }
}
?>