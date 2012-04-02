<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationTextState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationTextStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationTextState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationTextState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationTextStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationTextState
     */
    protected $textState;
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
        $this->textState            = new stubAnnotationTextState($this->mockAnnotationParser);
    }
    
    /**
     * test processing a line break
     *
     * @test
     */
    public function processLignBreak()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::DOCBLOCK));
        $this->textState->process("\n");
    }

    /**
     * test processing characters that should not change the state
     *
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->textState->process('a');
        $this->textState->process('1');
        $this->textState->process('(');
        $this->textState->process('[');
        $this->textState->process('_');
        $this->textState->process('.');
        $this->textState->process('*');
        $this->textState->process(' ');
    }
}
?>