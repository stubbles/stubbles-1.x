<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationTypeState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationTypeStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationTypeState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationTypeState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationTypeStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationTypeState
     */
    protected $annotationTypeState;
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
        $this->annotationTypeState  = new stubAnnotationTypeState($this->mockAnnotationParser);
    }

    /**
     * test processing a closing type parenthesis
     *
     * @test
     */
    public function processTypeParenthesis()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('setAnnotationType')
                                   ->with($this->equalTo('a'));
        $this->mockAnnotationParser->expects($this->exactly(2))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION));
        
        $this->annotationTypeState->process(']');
        $this->annotationTypeState->process('a');
        $this->annotationTypeState->process(']');
    }

    /**
     * test processing other characters
     *
     * @test
     */
    public function processOtherCharacters()
    {
        $this->annotationTypeState->process('a');
        $this->annotationTypeState->process('1');
        $this->annotationTypeState->process('_');
        $this->assertEquals('a1_', $this->annotationTypeState->getType());
    }

    /**
     * test processing other characters
     *
     * @test
     * @expectedException  ReflectionException
     */
    public function processOtherIllegalCharacters()
    {
        $this->annotationTypeState->process('a');
        $this->annotationTypeState->process('1');
        $this->annotationTypeState->process('_');
        $this->annotationTypeState->process(')');
        $this->annotationTypeState->process(']');
    }
}
?>