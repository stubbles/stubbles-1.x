<?php
/**
 * Test for net::stubbles::streams::stubAbstractDecoratedOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @version     $Id: stubAbstractDecoratedOutputStreamTestCase.php 2294 2009-08-20 20:43:15Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubAbstractDecoratedOutputStream');
/**
 * Helper class for the test to make abstract class instantiable.
 *
 * @package     stubbles
 * @subpackage  streams_test
 */
class TeststubAbstractDecoratedOutputStream extends stubAbstractDecoratedOutputStream
{
    // intentionally empty
}
/**
 * Test for net::stubbles::streams::stubAbstractDecoratedOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubAbstractDecoratedOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAbstractDecoratedOutputStream
     */
    protected $abstractDecoratedOutputStream;
    /**
     * mocked input stream
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockOutputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockOutputStream              = $this->getMock('stubOutputStream');
        $this->abstractDecoratedOutputStream = new TeststubAbstractDecoratedOutputStream($this->mockOutputStream);
    }

    /**
     * set() and get() enclosed output stream
     *
     * @test
     */
    public function setAndGetEnclosedOutputStream()
    {
        $this->assertSame($this->mockOutputStream, $this->abstractDecoratedOutputStream->getEnclosedOutputStream());
        $mockOutputStream2 = $this->getMock('stubOutputStream');
        $this->assertSame($this->abstractDecoratedOutputStream,
                          $this->abstractDecoratedOutputStream->setEnclosedOutputStream($mockOutputStream2)
        );
        $this->assertSame($mockOutputStream2, $this->abstractDecoratedOutputStream->getEnclosedOutputStream());
    }

    /**
     * data send write() should be written
     *
     * @test
     */
    public function write()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('write')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(3));
        $this->assertEquals(3, $this->abstractDecoratedOutputStream->write('foo'));
    }

    /**
     * data send writeLine() should be written
     *
     * @test
     */
    public function writeLine()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('writeLine')
                               ->with($this->equalTo('foo'))
                               ->will($this->returnValue(4));
        $this->assertEquals(4, $this->abstractDecoratedOutputStream->writeLine('foo'));
    }

    /**
     * close() should close the inner output stream
     *
     * @test
     */
    public function close()
    {
        $this->mockOutputStream->expects($this->once())
                               ->method('close');
        $this->abstractDecoratedOutputStream->close();
    }
}
?>