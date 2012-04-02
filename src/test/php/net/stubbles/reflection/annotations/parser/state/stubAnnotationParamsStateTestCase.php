<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamsState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationParamsStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationParamsState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamsState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationParamsStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationParamsState
     */
    protected $paramsState;
    /**
     * the mocked annotation parser
     *
     * @var  SimpleMock
     */
    protected $mockAnnotationParser;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockAnnotationParser = $this->getMock('stubAnnotationParser');
        $this->paramsState          = new stubAnnotationParamsState($this->mockAnnotationParser);
    }

    /**
     * test processing a closing value parenthesis
     *
     * @test
     */
    public function processClosingValueParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        $this->paramsState->process(')');
    }

    /**
     * test processing characters that should not change the state
     *
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramsState->process(',');
        $this->paramsState->process(' ');
        $this->paramsState->process("\r");
        $this->paramsState->process("\n");
        $this->paramsState->process("\t");
        $this->paramsState->process('*');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter1()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('a'));
        $this->paramsState->process('a');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter2()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('1'));
        $this->paramsState->process('1');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter3()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('('));
        $this->paramsState->process('(');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter4()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('['));
        $this->paramsState->process('[');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter5()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('_'));
        $this->paramsState->process('_');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacter6()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_NAME), $this->equalTo('.'));
        $this->paramsState->process('.');
    }
}
?>