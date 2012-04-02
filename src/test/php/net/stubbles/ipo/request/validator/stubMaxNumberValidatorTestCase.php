<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubMaxNumberValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubMaxNumberValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubMaxNumberValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubMaxNumberValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubMaxNumberValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMaxNumberValidator
     */
    protected $maxNumberValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->maxNumberValidator = new stubMaxNumberValidator(5);
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertTrue($this->maxNumberValidator->validate(3));
        $this->assertTrue($this->maxNumberValidator->validate(4));
        $this->assertTrue($this->maxNumberValidator->validate(4.99));
        $this->assertTrue($this->maxNumberValidator->validate(5));
        $this->assertFalse($this->maxNumberValidator->validate(5.1));
        $this->assertFalse($this->maxNumberValidator->validate(6));
        $this->assertFalse($this->maxNumberValidator->validate(10));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('maxNumber' => 5), $this->maxNumberValidator->getCriteria());
    }
}
?>