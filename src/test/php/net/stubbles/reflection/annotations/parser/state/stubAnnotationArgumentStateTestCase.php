<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationArgumentState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationArgumentStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationArgumentState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationArgumentState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationArgumentStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationArgumentState
     */
    protected $annotationArgumentState;
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
        $this->mockAnnotationParser    = $this->getMock('stubAnnotationParser');
        $this->annotationArgumentState = new stubAnnotationArgumentState($this->mockAnnotationParser);
    }

    /**
     * test processing a closing argument parenthesis
     *
     * @test
     */
    public function processArgumentParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationForArgument')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION));
                                   
        $this->annotationArgumentState->process('}');
        $this->annotationArgumentState->process('a');
        $this->annotationArgumentState->process('}');
    }

    /**
     * test processing other characters
     *
     * @test
     */
    public function processOtherCharacters()
    {
        $this->annotationArgumentState->process('a');
        $this->annotationArgumentState->process('1');
        $this->annotationArgumentState->process('_');
        $this->assertEquals('a1_', $this->annotationArgumentState->getArgument());
    }

    /**
     * test processing other characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processIllegalCharacters()
    {
        $this->annotationArgumentState->process('a');
        $this->annotationArgumentState->process('1');
        $this->annotationArgumentState->process('_');
        $this->assertEquals('a1_', $this->annotationArgumentState->getArgument());
        $this->annotationArgumentState->process(')');
        $this->annotationArgumentState->process('}');
    }
}
?>