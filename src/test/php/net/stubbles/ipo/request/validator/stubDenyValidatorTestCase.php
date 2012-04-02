<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubDenyValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubDenyValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubDenyValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubDenyValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubDenyValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubDenyValidator
     */
    protected $denyValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->denyValidator = new stubDenyValidator();
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertFalse($this->denyValidator->validate(123));
        $this->assertFalse($this->denyValidator->validate('1234'));
        $this->assertFalse($this->denyValidator->validate(true));
        $this->assertFalse($this->denyValidator->validate(null));
        $this->assertFalse($this->denyValidator->validate(new stdClass()));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array(), $this->denyValidator->getCriteria());
    }
}
?>