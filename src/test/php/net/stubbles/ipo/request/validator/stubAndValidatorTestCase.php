<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubAndValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubAndValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubAndValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubAndValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubAndValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAndValidator
     */
    protected $andValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->andValidator = new stubAndValidator();
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $mockValidator1 = $this->getMock('stubValidator');
        $mockValidator1->expects($this->exactly(2))->method('validate')->will($this->returnValue(true));
        $this->andValidator->addValidator($mockValidator1);
        $this->assertTrue($this->andValidator->validate('foo'));
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->once())->method('validate')->will($this->returnValue(false));
        $this->andValidator->addValidator($mockValidator2);
        $this->assertFalse($this->andValidator->validate('foo'));
    }

    /**
     * assure that validation works correct
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function validationException()
    {
        $this->andValidator->validate('foo');
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
        $this->andValidator->addValidator($mockValidator1);
        $mockValidator2 = $this->getMock('stubValidator');
        $mockValidator2->expects($this->once())->method('getCriteria')->will($this->returnValue(array('bar' => 'baz')));
        $this->andValidator->addValidator($mockValidator2);
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $this->andValidator->getCriteria());
    }
}
?>