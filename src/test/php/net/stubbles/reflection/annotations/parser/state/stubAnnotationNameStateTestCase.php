<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationNameState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationNameStateTestCase.php 2140 2009-03-20 14:50:50Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationNameState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationNameState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationNameStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationNameState
     */
    protected $annotationNameState;
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
        $this->annotationNameState  = new stubAnnotationNameState($this->mockAnnotationParser);
    }

    /**
     * test processing a space element
     *
     * @test
     */
    public function processSpaceAtStart()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        $this->mockAnnotationParser->expects($this->never())->method('registerAnnotation');
        
        $this->annotationNameState->process(' ');
    }

    /**
     * test processing a space element
     *
     * @test
     */
    public function processSpace()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        
        $this->annotationNameState->process('a');
        $this->annotationNameState->process(' ');
    }

    /**
     * test processing a space element
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processSpaceAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process(' ');
    }

    /**
     * test processing a line break
     *
     * @test
     */
    public function processLineBreak()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process("\n");
        $this->annotationNameState->process('a');
        $this->annotationNameState->process("\n");
    }

    /**
     * test processing a line break
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processLineBreakAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process("\n");
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process("\n");
    }

    /**
     * test processing a carriage return
     *
     * @test
     */
    public function processCarriageReturn()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process("\r");
        $this->annotationNameState->process('a');
        $this->annotationNameState->process("\r");
    }

    /**
     * test processing a carriage return
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processCarriageReturnAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process("\r");
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process("\r");
    }

    /**
     * test processing an argument parenthesis
     *
     * @test
     */
    public function processArgumentParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ARGUMENT));
        
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('{');
    }

    /**
     * test processing an argument parenthesis
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processArgumentParenthesisAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('{');
    }

    /**
     * test processing an argument parenthesis
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processArgumentParenthesisAfterSelected()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->selected();
        $this->annotationNameState->process('{');
    }

    /**
     * test processing a type parenthesis
     *
     * @test
     */
    public function processTypeParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION_TYPE));
        
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('[');
    }

    /**
     * test processing a type parenthesis
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processTypeParenthesisAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('[');
    }

    /**
     * test processing a type parenthesis
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processTypeParenthesisAfterSelected()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->selected();
        $this->annotationNameState->process('[');
    }

    /**
     * test processing a value parenthesis
     *
     * @test
     */
    public function processValueParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('(');
    }

    /**
     * test processing a value parenthesis
     *
     * @test
     * @group  bug202
     * @see    http://stubbles.net/ticket/202
     */
    public function processValueParenthesisAfterForbiddenAnnotation()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('registerAnnotation')
                                   ->with($this->equalTo('return'));
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('e');
        $this->annotationNameState->process('t');
        $this->annotationNameState->process('u');
        $this->annotationNameState->process('r');
        $this->annotationNameState->process('n');
        $this->annotationNameState->process('(');
    }

    /**
     * test processing a value parenthesis
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processValueParenthesisFailsAfterSelected()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->selected();
        $this->annotationNameState->process('(');
    }

    /**
     * test processing other characters
     *
     * @test
     */
    public function processOtherCharacters()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->assertEquals('a1_', $this->annotationNameState->getName());
    }

    /**
     * test processing illegal characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharactersFollowedBySpace()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process(' ');
    }

    /**
     * test processing illegal characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharactersFollowedByLineBreak()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process("\n");
    }

    /**
     * test processing illegal characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharactersFollowedByArgumentParenthesis()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('{');
    }

    /**
     * test processing illegal characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharactersFollowedByTypeParenthesis()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('[');
    }

    /**
     * test processing illegal characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharactersFollowedByValueParenthesis()
    {
        $this->annotationNameState->process('a');
        $this->annotationNameState->process('1');
        $this->annotationNameState->process('_');
        $this->annotationNameState->process(')');
        $this->annotationNameState->process('(');
    }
}
?>