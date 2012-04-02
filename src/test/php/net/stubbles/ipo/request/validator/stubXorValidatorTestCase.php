<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubXorValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubXorValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubXorValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubXorValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubXorValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXorValidator
     */
    protected $xorValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->xorValidator = new stubXorValidator();
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $mockValidator1 = $this->getMock('stubValidator');
        $mockValidator1->expects($this->exactly(4))->method('validate')->will($this->returnValue(false));
        $this->xorValidator->addValidator($mockValidator1);
        $this->assertFalse($this->xorValidator->validate('foo'));
        
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->exactly(3))->method('validate')->will($this->returnValue(true));
        $this->xorValidator->addValidator($mockValidator2);
        $this->assertTrue($this->xorValidator->validate('foo'));
        
        $mockValidator3 = $this->getMock('stubValidator');
        $mockValidator3->expects($this->exactly(2))->method('validate')->will($this->returnValue(false));
        $this->xorValidator->addValidator($mockValidator3);
        $this->assertTrue($this->xorValidator->validate('foo'));
        
        $mockValidator4 = $this->getMock('stubValidator');
        $mockValidator4->expects($this->once())->method('validate')->will($this->returnValue(true));
        $this->xorValidator->addValidator($mockValidator4);
        $this->assertFalse($this->xorValidator->validate('foo'));
    }

    /**
     * assure that validation works correct
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function validationException()
    {
        $this->xorValidator->validate('foo');
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
        $this->xorValidator->addValidator($mockValidator1);
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->once())->method('getCriteria')->will($this->returnValue(array('bar' => 'baz')));
        $this->xorValidator->addValidator($mockValidator2);
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $this->xorValidator->getCriteria());
    }
}
?>