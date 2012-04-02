<?php
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationDocblockState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @version     $Id: stubAnnotationDocblockStateTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::reflection::annotations::parser::state::stubAnnotationDocblockState');
/**
 * Test for net::stubbles::reflection::annotations::parser::state::stubAnnotationDocblockState.
 *
 * @package     stubbles
 * @subpackage  reflection_annotations_parser_state_test
 * @group       reflection
 * @group       reflection_annotations
 */
class stubAnnotationDocblockStateTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAnnotationDocblockState
     */
    protected $docblockState;
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
        $this->docblockState        = new stubAnnotationDocblockState($this->mockAnnotationParser);
    }

    /**
     * test processing a @
     *
     * @test
     */
    public function processAtSign()
    {
        $this->mockAnnotationParser->expects($this->once())
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::ANNOTATION_NAME));
        $this->docblockState->process('@');
    }

    /**
     * test processing word characters
     *
     * @test
     */
    public function processStateChangingCharacterCharacter()
    {
        $this->mockAnnotationParser->expects($this->exactly(6))
                                   ->method('changeState')
                                   ->with($this->equalTo(stubAnnotationState::TEXT));
        $this->docblockState->process('a');
        $this->docblockState->process('1');
        $this->docblockState->process('(');
        $this->docblockState->process('[');
        $this->docblockState->process('_');
        $this->docblockState->process('.');
    }

    /**
     * test processing characters that should not change the state
     *
     * @test
     */
    public function processNoneStateChangingCharacters()
    {
        $this->mockAnnotationParser->expects($this->never())->method('changeState');
        $this->docblockState->process('*');
        $this->docblockState->process(' ');
        $this->docblockState->process("\n");
        $this->docblockState->process("\t");
    }
}
?>