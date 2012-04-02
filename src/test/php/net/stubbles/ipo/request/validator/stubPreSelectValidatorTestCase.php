<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubPreSelectValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubPreSelectValidatorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubPreSelectValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubPreSelectValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubPreSelectValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubPreSelectValidator
     */
    protected $preSelectValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->preSelectValidator = new stubPreSelectValidator(array('foo', 'bar'));
    }

    /**
     * allowed values should be as given in constructor
     *
     * @test
     */
    public function allowedValues()
    {
        $this->assertEquals(array('foo', 'bar'), $this->preSelectValidator->getAllowedValues());
    }

    /**
     * assure that validation works correct
     *
     * @test
     */
    public function validation()
    {
        $this->assertTrue($this->preSelectValidator->validate('foo'));
        $this->assertTrue($this->preSelectValidator->validate('bar'));
        $this->assertTrue($this->preSelectValidator->validate(array('bar', 'foo')));
        $this->assertFalse($this->preSelectValidator->validate('baz'));
        $this->assertFalse($this->preSelectValidator->validate(null));
        $this->assertFalse($this->preSelectValidator->validate(array('bar', 'foo', 'baz')));
    }

    /**
     * assure that returning the criterias works correct
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('allowedValues' => array('foo', 'bar')), $this->preSelectValidator->getCriteria());
    }
}
?>