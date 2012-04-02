<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamNameState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationParamNameStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationParamNameState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamNameState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationParamNameStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationParamNameState
     */
    protected $paramNameState;
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
        $this->paramNameState       = new stubAnnotationParamNameState($this->mockAnnotationParser);
    }

    /**
     * test processing quotation marks
     *
     * @test
     */
    public function processSimpleQuotationMarks()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('value'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_VALUE), $this->equalTo("'"));
        
        $this->paramNameState->process("'");
    }

    /**
     * test processing quotation marks
     *
     * @test
     */
    public function processQuotationMarks()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('value'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_VALUE), $this->equalTo('"'));
        
        $this->paramNameState->process('"');
    }

    /**
     * test processing quotation marks
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processQuotationMarksInvalidOffset()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('a');
        $this->paramNameState->process("'");
    }

    /**
     * test processing quotation marks
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processQuotationMarksOnInvalidOffset()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('a');
        $this->paramNameState->process("'");
    }

    /**
     * test processing the equal sign
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processEqualSignOnStart()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('=');
    }

    /**
     * test processing the equal sign
     *
     * @test
     */
    public function processEqualSignAfterCorrectParamName()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotationParam')
                                   ->with($this->equalTo('abc_123'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAM_VALUE));
        $this->paramNameState->process('abc_123');
        $this->paramNameState->process('=');
    }

    /**
     * test processing the equal sign
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processEqualSignAfterInCorrectParamName()
    {
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotationParam');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramNameState->process('1a');
        $this->paramNameState->process('=');
    }

    /**
     * test processing a closing value parenthesis
     *
     * @test
     */
    public function processClosingValueParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerSingleAnnotationParam')
                                   ->with($this->equalTo('a'), $this->equalTo(false));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
                                   
        $this->paramNameState->process(')');
        $this->paramNameState->process('a');
        $this->paramNameState->process(')');
    }

    /**
     * test processing other characters
     *
     * @test
     */
    public function processOtherCharacters()
    {
        $this->paramNameState->process('a');
        $this->paramNameState->process('1');
        $this->paramNameState->process('_');
        $this->assertEquals('a1_', $this->paramNameState->getName());
    }
}
?>