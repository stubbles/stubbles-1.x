<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamValueState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationParamValueStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationParamValueState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationParamValueState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationParamValueStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationParamValueState
     */
    protected $paramValueState;
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
        $this->paramValueState      = new stubAnnotationParamValueState($this->mockAnnotationParser);
    }

    /**
     * test processing single quotation marks on start of the value
     *
     * @test
     */
    public function processSingleQuotationMarksOnStart()
    {
        $this->assertEquals('', $this->paramValueState->getValue());
        $this->assertNull($this->paramValueState->getEnclosed());
        $this->assertFalse($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
        $this->paramValueState->process("'");
        $this->assertEquals('', $this->paramValueState->getValue());
        $this->assertEquals("'", $this->paramValueState->getEnclosed());
        $this->assertTrue($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
    }

    /**
     * test processing single quotation marks in between of the value
     *
     * @test
     */
    public function processSingleQuotationMarksInBetween()
    {
        $this->paramValueState->process('a');
        $this->paramValueState->process("'");
        $this->paramValueState->process('b');
        $this->assertEquals("a'b", $this->paramValueState->getValue());
        $this->assertNull($this->paramValueState->getEnclosed());
        $this->assertFalse($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
    }

    /**
     * test processing double quotation marks on start of the value
     *
     * @test
     */
    public function processDoubleQuotationMarksOnStart()
    {
        $this->assertEquals('', $this->paramValueState->getValue());
        $this->assertNull($this->paramValueState->getEnclosed());
        $this->assertFalse($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
        $this->paramValueState->process('"');
        $this->assertEquals('', $this->paramValueState->getValue());
        $this->assertEquals('"', $this->paramValueState->getEnclosed());
        $this->assertTrue($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
    }

    /**
     * test processing double quotation marks in between of the value
     *
     * @test
     */
    public function processDoubleQuotationMarksInBetween()
    {
        $this->paramValueState->process('a');
        $this->paramValueState->process('"');
        $this->paramValueState->process('b');
        $this->assertEquals('a"b', $this->paramValueState->getValue());
        $this->assertNull($this->paramValueState->getEnclosed());
        $this->assertFalse($this->paramValueState->isString());
        $this->assertFalse($this->paramValueState->isNextCharacterEscaped());
    }

    /**
     * test processing a param seperator that is not enclosed in quotation marks
     *
     * @test
     */
    public function processParamSeperatorNotEnclosedOnFirstOffset()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo(''), $this->equalTo(false));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        
        $this->paramValueState->process(',');
    }

    /**
     * test processing a param seperator that is not enclosed in quotation marks
     *
     * @test
     */
    public function processParamSeperatorNotEnclosedAfterValue()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo('a'), $this->equalTo(false));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        
        $this->paramValueState->process('a');
        $this->paramValueState->process(',');
    }

    /**
     * test processing a param seperator that is enclosed within quotation marks
     *
     * @test
     */
    public function processParamSeperatorWithinEnclosed()
    {
        $this->mockAnnotationParser->expects($this->never())->method('setAnnotationParamValue');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramValueState->process('"');
        $this->paramValueState->process('a');
        $this->paramValueState->process(',');
        $this->paramValueState->process('b');
        $this->assertEquals('a,b', $this->paramValueState->getValue());
    }

    /**
     * test processing a closing value parenthesis that is not enclosed in quotation marks
     *
     * @test
     */
    public function processClosingValueParenthesisNotEnclosedFirstOffset()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo(''), $this->equalTo(false));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
                                   
        $this->paramValueState->process(')');
    }

    /**
     * test processing a closing value parenthesis that is not enclosed in quotation marks
     *
     * @test
     */
    public function processClosingValueParenthesisNotEnclosed()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo('a'), $this->equalTo(false));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
                                   
        $this->paramValueState->process('a');
        $this->paramValueState->process(')');
    }

    /**
     * test processing a closing value parenthesis that is enclosed within quotation marks
     *
     * @test
     */
    public function processClosingValueParenthesisWithinEnclosed()
    {
        $this->mockAnnotationParser->expects($this->never())->method('setAnnotationParamValue');
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->paramValueState->process('"');
        $this->paramValueState->process('a');
        $this->paramValueState->process(')');
        $this->paramValueState->process('b');
        $this->assertEquals('a)b', $this->paramValueState->getValue());
    }

    /**
     * test processing an enclosed value
     *
     * @test
     */
    public function processEnclosedValue()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo("a'b"), $this->equalTo(true));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        $this->paramValueState->process('"');
        $this->paramValueState->process('a');
        $this->paramValueState->process("'");
        $this->paramValueState->process('b');
        $this->paramValueState->process('"');
    }

    /**
     * test processing an enclosed value that contains an escaped character
     *
     * @test
     */
    public function processEnclosedValueWithEscapedCharacter()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationParamValue')
                                   ->with($this->equalTo('a"b'), $this->equalTo(true));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        $this->paramValueState->process('"');
        $this->paramValueState->process('a');
        $this->paramValueState->process('\\');
        $this->paramValueState->process('"');
        $this->paramValueState->process('b');
        $this->paramValueState->process('"');
    }
}
?>