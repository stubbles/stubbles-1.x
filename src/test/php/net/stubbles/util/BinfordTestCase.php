<?php
/**
 * Tests for net::stubbles::util::Binford.
 *
 * @package     stubbles
 * @subpackage  util_test
 * @version     $Id: BinfordTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::util::Binford');
/**
 * Tests for net::stubbles::util::Binford.
 *
 * @package     stubbles
 * @subpackage  util_test
 * @group       binford
 */
class BinfordTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  Binford
     */
    protected $binford;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->binford = new Binford();
    }

    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function validate()
    {
        $this->assertTrue($this->binford->validate(Binford::POWER));
        $this->assertTrue($this->binford->validate('Binford'));
        $this->assertTrue($this->binford->validate('Binford ' . Binford::POWER));
        $this->assertFalse($this->binford->validate('Bob Vila'));
    }

    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function getCriteria()
    {
        $this->assertEquals(array('allowedValues' => array(Binford::POWER, 'Binford', 'Binford ' . Binford::POWER)), $this->binford->getCriteria());
    }

    /**
     * assure that values are returned as expected
     *
     * @test
     */
    public function filter()
    {
        $this->assertEquals(Binford::POWER, $this->binford->execute(Binford::POWER));
        $this->assertEquals('Binford', $this->binford->execute('Binford'));
        $this->assertEquals('Binford ' . Binford::POWER, $this->binford->execute('Binford ' . Binford::POWER));
        $this->assertEquals('Binford ' . Binford::POWER, $this->binford->execute('Bob Vila'));
    }

    /**
     * post process adds a binford header
     *
     * @test
     */
    public function postProcess()
    {
        $mockResponse = $this->getMock('stubResponse');
        $mockResponse->expects($this->once())
                     ->method('addHeader')
                     ->with($this->equalTo('X-Binford'), $this->equalTo(Binford::POWER));
        $this->binford->postProcess($this->getMock('stubRequest'), $this->getMock('stubSession'), $mockResponse);
    }

    /**
     * assert that the binford hash code always equals the power of binford
     *
     * @test
     */
    public function hashCode()
    {
        $this->assertEquals(Binford::POWER, $this->binford->hashCode());
    }

    /**
     * assure that any Binford instance is equal to any other
     *
     * @test
     */
    public function equals()
    {
        $this->assertTrue($this->binford->equals($this->binford));
        $binford = new Binford();
        $this->assertTrue($this->binford->equals($binford));
        $this->assertTrue($binford->equals($this->binford));
        $this->assertFalse($this->binford->equals('Bob Vila'));
    }

    /**
     * string representation
     *
     * @test
     */
    public function stringOf()
    {
        $this->assertEquals("net::stubbles::util::Binford {\n    POWER(integer): 6100\n}\n", (string) $this->binford);
    }
}
?>