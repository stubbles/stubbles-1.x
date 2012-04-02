<?php
/**
 * Tests for net::stubbles::ipo::request::validator::stubIpValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @version     $Id: stubIpValidatorTestCase.php 3134 2011-07-26 18:27:28Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::validator::stubIpValidator');
/**
 * Tests for net::stubbles::ipo::request::validator::stubIpValidator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_validator_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_validator
 */
class stubIpValidatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubIpValidator
     */
    protected $ipValidator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->ipValidator = new stubIpValidator();
    }

    /**
     * @test
     */
    public function stringIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('foo'));
    }

    /**
     * @test
     */
    public function nullIsNoIpAndResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate(null));
    }

    /**
     * @test
     */
    public function booleansAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipValidator->validate(true));
        $this->assertFalse($this->ipValidator->validate(false));
    }

    /**
     * @test
     */
    public function singleNumbersAreNoIpAndResultInFalse()
    {
        $this->assertFalse($this->ipValidator->validate(4));
        $this->assertFalse($this->ipValidator->validate(6));
    }

    /**
     * @test
     */
    public function invalidIpV4WithMissingPartResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('255.55.55'));
    }

    /**
     * @test
     */
    public function invalidIpV4WithSuperflousPartResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('111.222.333.444.555'));
    }

    /**
     * @test
     */
    public function invalidIpV4WithMissingNumberResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('1..3.4'));
    }

    /**
     * @test
     */
    public function greatestIpV4ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('255.255.255.255'));
    }

    /**
     * @test
     */
    public function lowestIpV4ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('0.0.0.0'));
    }

    /**
     * @test
     */
    public function correctIpV4ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('1.2.3.4'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithMissingPartResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate(':1'));
    }

    /**
     * @test
     */
    public function invalidIpV6ResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('::ffffff:::::a'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadAtStartResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('XXXX::a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadAtEndResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('9982::a574:382b:23c1:aa49:4592:4efe:XXXX'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexquadResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('a574::XXXX:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function invalidIpV6WithHexDigitResultsInFalse()
    {
        $this->assertFalse($this->ipValidator->validate('a574::382X:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function correctIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('febc:a574:382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function localhostIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('::1'));
    }

    /**
     * @test
     */
    public function shortenedIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('febc:a574:382b::4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function evenMoreShortenedIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('febc::23c1:aa49:0:0:9982'));
    }

    /**
     * @test
     */
    public function singleShortenedIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('febc:a574:2b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPrefixIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('::382b:23c1:aa49:4592:4efe:9982'));
    }

    /**
     * @test
     */
    public function shortenedPostfixIpV6ResultsInTrue()
    {
        $this->assertTrue($this->ipValidator->validate('febc:a574:382b:23c1:aa49::'));
    }

    /**
     * @test
     */
    public function hasNoCriteria()
    {
        $this->assertEquals(array(), $this->ipValidator->getCriteria());
    }
}
?>