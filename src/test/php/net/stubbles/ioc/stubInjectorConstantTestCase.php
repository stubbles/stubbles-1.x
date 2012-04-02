<?php
/**
 * Test for net::stubbles::ioc::stubInjector with constant binding.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorConstantTestCase.php 3073 2011-02-28 22:28:42Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder',
                      'net::stubbles::ioc::stubValueInjectionProvider'
);
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorConstantTestCase_Question
{
    /**
     * the answer
     *
     * @var  mixed
     */
    private $answer;

    /**
     * sets the answer
     *
     * @param  mixed  $answer
     * @Inject
     * @Named('answer')
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
    }

    /**
     * returns the answer
     *
     * @return  mixed
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @since       1.6.0
 */
class stubInjectorAnswerConstantProvider extends stubBaseObject implements stubInjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return 42;
    }
}
/**
 * Test for net::stubbles::ioc::stubInjector with constant binding.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorConstantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injectConstant()
    {
        $binder = new stubBinder();
        $binder->bindConstant()->named('answer')->to(42);
        $injector = $binder->getInjector();
        $question = $injector->getInstance('stubInjectorConstantTestCase_Question');
        $this->assertInstanceOf('stubInjectorConstantTestCase_Question', $question);
        $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * @test
     */
    public function checkForNonExistingConstantBindingShouldReturnFalse()
    {
        $injector = new stubInjector();
        $this->assertFalse($injector->hasBinding(stubConstantBinding::TYPE, 'test'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function shortcutForRetrievingConstantValues()
    {
        $binder = new stubBinder();
        $injector = $binder->getInjector();
        $this->assertFalse($injector->hasConstant('answer'));
        $binder->bindConstant()->named('answer')->to(42);
        $this->assertTrue($injector->hasConstant('answer'));
        $this->assertEquals(42, $injector->getConstant('answer'));
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderInstance()
    {
        $binder = new stubBinder();
        $binder->bindConstant()
               ->named('answer')
               ->toProvider(new stubValueInjectionProvider(42));
        $injector = $binder->getInjector();
        $this->assertTrue($injector->hasConstant('answer'));
        $this->assertEquals(42, $injector->getConstant('answer'));
        $question = $injector->getInstance('stubInjectorConstantTestCase_Question');
        $this->assertInstanceOf('stubInjectorConstantTestCase_Question', $question);
        $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * @test
     * @expectedException  stubBindingException
     * @group              ioc_constantprovider
     * @since              1.6.0
     */
    public function constantViaInvalidInjectionProviderClassThrowsBindingException()
    {
        $binder = new stubBinder();
        $binder->bindConstant()
               ->named('answer')
               ->toProviderClass('stdClass');
        $binder->getInjector()->getConstant('answer');
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderClass()
    {
        $binder = new stubBinder();
        $binder->bindConstant()
               ->named('answer')
               ->toProviderClass(new stubReflectionClass('stubInjectorAnswerConstantProvider'));
        $injector = $binder->getInjector();
        $this->assertTrue($injector->hasConstant('answer'));
        $this->assertEquals(42, $injector->getConstant('answer'));
        $question = $injector->getInstance('stubInjectorConstantTestCase_Question');
        $this->assertInstanceOf('stubInjectorConstantTestCase_Question', $question);
        $this->assertEquals(42, $question->getAnswer());
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderClassName()
    {
        $binder = new stubBinder();
        $binder->bindConstant()
               ->named('answer')
               ->toProviderClass('stubInjectorAnswerConstantProvider');
        $injector = $binder->getInjector();
        $this->assertTrue($injector->hasConstant('answer'));
        $this->assertEquals(42, $injector->getConstant('answer'));
        $question = $injector->getInstance('stubInjectorConstantTestCase_Question');
        $this->assertInstanceOf('stubInjectorConstantTestCase_Question', $question);
        $this->assertEquals(42, $question->getAnswer());
    }
}
?>