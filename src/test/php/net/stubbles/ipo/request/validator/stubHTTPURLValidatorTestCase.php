<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubHTTPURLValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubHTTPURLValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubHTTPURLValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubHTTPURLValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubHTTPURLValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubHTTPURLValidator
     */
    protected $httpURLValidator;
    
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->httpURLValidator = new stubHTTPURLValidator();
    }
    
    /**
     * empty values will result in false
     *
     * @test
     */
    public function emptyValueValidatesToFalse()
    {
        $this->assertFalse($this->httpURLValidator->validate(null));
        $this->assertFalse($this->httpURLValidator->validate(''));
    }

    /**
     * invalid values will result in false
     *
     * @test
     */
    public function invalidValueValidatesToFalse()
    {
        $this->assertFalse($this->httpURLValidator->validate('invalid'));
    }

    /**
     * valid values will result in true
     *
     * @test
     */
    public function validValueValidatesToTrue()
    {
        $this->assertTrue($this->httpURLValidator->validate('http://example.net/'));
    }

    /**
     * the validator does not have any criterion
     *
     * @test
     */
    public function hasNoCriterion()
    {
        $this->assertEquals(array(), $this->httpURLValidator->getCriteria());
    }
}
?>