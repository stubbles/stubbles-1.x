<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubMinNumberValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubMinNumberValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubMinNumberValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubMinNumberValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubMinNumberValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubMinNumberValidator
     */
    protected $minNumberValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->minNumberValidator = new stubMinNumberValidator(5);
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertFalse($this->minNumberValidator->validate(3));
        $this->assertFalse($this->minNumberValidator->validate(4));
        $this->assertFalse($this->minNumberValidator->validate(4.99));
        $this->assertTrue($this->minNumberValidator->validate(5));
        $this->assertTrue($this->minNumberValidator->validate(5.1));
        $this->assertTrue($this->minNumberValidator->validate(6));
        $this->assertTrue($this->minNumberValidator->validate(10));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('minNumber' => 5), $this->minNumberValidator->getCriteria());
    }
}
?>