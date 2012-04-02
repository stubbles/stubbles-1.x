<?php
/**
 * Test for net::stubbles::ioc::stubInjector with provider binding.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @version     $Id: stubInjectorProviderTestCase.php 2918 2011-01-13 21:43:40Z mikey $
 */
stubClassLoader::load('net::stubbles::ioc::stubBinder');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorProviderTestCase_Answer
{
    /**
     * the answer to all questions
     *
     * @return  int
     */
    public function answer()
    {
        return 42;
    }
}
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 */
class stubInjectorProviderTestCase_Question
{
    /**
     * answer
     *
     * @var  stubInjectorProviderTestCase_Answer
     */
    private $answer;

    /**
     * @param  stubInjectorProviderTestCase_Answer  $answer
     * @Inject
     * @Named('answer')
     */
    public function setAnswer(stubInjectorProviderTestCase_Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * returns answer
     *
     * @return  stubInjectorProviderTestCase_Answer
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
 */
class MyProviderClass extends stubBaseObject implements stubInjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new stubInjectorProviderTestCase_Answer();
    }
}
/**
 * Test for net::stubbles::ioc::stubInjector with provider binding.
 *
 * @package     stubbles
 * @subpackage  ioc_test
 * @group       ioc
 */
class stubInjectorProviderTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * use a provider for the injection
     *
     * @test
     */
    public function injectWithProvider()
    {
        $binder       = new stubBinder();
        $mockProvider = $this->getMock('stubInjectionProvider');
        $answer       = new stubInjectorProviderTestCase_Answer();
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->with($this->equalTo('answer'))
                     ->will($this->returnValue($answer));
        $binder->bind('stubInjectorProviderTestCase_Answer')->toProvider($mockProvider);
        $question = $binder->getInjector()->getInstance('stubInjectorProviderTestCase_Question');
        $this->assertInstanceOf('stubInjectorProviderTestCase_Question', $question);
        $this->assertSame($answer, $question->getAnswer());
    }

    /**
     * using an invalid provider class throws an exception
     *
     * @test
     * @expectedException  stubBindingException
     */
    public function injectWithInvalidProviderClassThrowsException()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorProviderTestCase_Answer')->toProviderClass('stdClass');
        $binder->getInjector()->getInstance('stubInjectorProviderTestCase_Question');
    }

    /**
     * injection with a provider class
     *
     * @test
     */
    public function injectWithProviderClass()
    {
        $binder = new stubBinder();
        $binder->bind('stubInjectorProviderTestCase_Answer')->toProviderClass('MyProviderClass');
        $question = $binder->getInjector()->getInstance('stubInjectorProviderTestCase_Question');
        $this->assertInstanceOf('stubInjectorProviderTestCase_Question', $question);
        $this->assertInstanceOf('stubInjectorProviderTestCase_Answer', $question->getAnswer());
    }
}
?>