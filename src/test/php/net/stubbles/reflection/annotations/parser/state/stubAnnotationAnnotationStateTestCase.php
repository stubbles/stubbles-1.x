<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationAnnotationState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationAnnotationStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationAnnotationState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationAnnotationState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationAnnotationStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationAnnotationState
     */
    protected $annotationState;
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
        $this->annotationState      = new stubAnnotationAnnotationState($this->mockAnnotationParser);
    }

    /**
     * test processing a line break
     *
     * @test
     */
    public function processLinebreak()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        $this->annotationState->process("\n");
    }

    /**
     * test processing an argument parenthesis
     *
     * @test
     */
    public function processArgumentParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ARGUMENT));
        $this->annotationState->process('{');
    }

    /**
     * test processing a type parenthesis
     *
     * @test
     */
    public function processTypeParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION_TYPE));
        $this->annotationState->process('[');
    }

    /**
     * test processing a value parenthesis
     *
     * @test
     */
    public function processValueParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::PARAMS));
        $this->annotationState->process('(');
    }

    /**
     * test processing a value parenthesis
     *
     * @test
     */
    public function processOtherCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->annotationState->process('a');
        $this->annotationState->process('1');
        $this->annotationState->process(']');
        $this->annotationState->process(')');
        $this->annotationState->process('_');
        $this->annotationState->process('.');
    }
}
?>