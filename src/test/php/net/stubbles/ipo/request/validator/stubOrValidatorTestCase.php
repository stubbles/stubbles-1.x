<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubOrValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubOrValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubOrValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubOrValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubOrValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubOrValidator
     */
    protected $orValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->orValidator = new stubOrValidator();
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $mockValidator1 = $this->getMock('stubValidator');
        $mockValidator1->expects($this->exactly(2))->method('validate')->will($this->returnValue(false));
        $this->orValidator->addValidator($mockValidator1);
        $this->assertFalse($this->orValidator->validate('foo'));
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->orValidator->addValidator($mockValidator2);
        $this->assertTrue($this->orValidator->validate('foo'));
    }

    /**
     * assure that validation works correct
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function validationException()
    {
        $this->orValidator->validate('foo');
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $mockValidator1 = $this->getMock('stubValidator');
        $mockValidator1->expects($this->once())->method('getCriteria')->will($this->returnValue(array('foo' => 'bar')));
        $this->orValidator->addValidator($mockValidator1);
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->once())->method('getCriteria')->will($this->returnValue(array('bar' => 'baz')));
        $this->orValidator->addValidator($mockValidator2);
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $this->orValidator->getCriteria());
    }
}
?>