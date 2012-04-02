<?php
/**
 * Test for net::stubbles::streams::stubResourceOutputStream.
 *
 * @author      Frank Kleine mikey@stubbles.net
 * @package     stubbles
 * @subpackage  streams_test
 */
stubClassLoader::load('net::stubbles::streams::stubResourceOutputStream');
@include_once 'vfsStream/vfsStream.php';
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  streams_test
 */
class TeststubResourceOutputStream extends stubResourceOutputStream
{
    /**
     * constructor
     *
     * @param   resource  $handle
     */
    public function __construct($handle)
    {
        $this->setHandle($handle);
    }
}
/**
 * Test for net::stubbles::streams::stubResourceOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_test
 * @group       streams
 */
class stubResourceOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  TeststubResourceOutputStream
     */
    protected $resourceOutputStream;
    /**
     * the handle
     *
     * @var  resource
     */
    protected $handle;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->handle               = fopen(dirname(__FILE__) . '/test_write.txt', 'w');
        $this->resourceOutputStream = new TeststubResourceOutputStream($this->handle);
    }

    /**
     * try to create an instance with an invalid handle
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function invalidHandle()
    {
        $resourceOutputStream = new TeststubResourceOutputStream('invalid');
    }

    /**
     * try to write to an already closed stream
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function writeToClosedStream()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * try to write to an already closed stream
     *
     * @test
     * @expectedException  stubIllegalStateException
     */
    public function writeLineToClosedStream()
    {
        $this->resourceOutputStream->close();
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * try to write to an already closed stream
     *
     * @test
     * @expectedException  stubIOException
     */
    public function writeToExternalClosedStream()
    {
        fclose($this->handle);
        $this->resourceOutputStream->write('foobarbaz');
    }

    /**
     * try to write to an already closed stream
     *
     * @test
     * @expectedException  stubIOException
     */
    public function writeLineToExternalClosedStream()
    {
        fclose($this->handle);
        $this->resourceOutputStream->writeLine('foobarbaz');
    }

    /**
     * write some stuff into stream
     *
     * @test
     */
    public function write()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('home'));
        $file = vfsStream::newFile('test.txt')->at(vfsStreamWrapper::getRoot());
        $resourceOutputStream = new TeststubResourceOutputStream(fopen(vfsStream::url('home/test.txt'), 'w'));
        $this->assertEquals(9, $resourceOutputStream->write('foobarbaz'));
        $this->assertEquals('foobarbaz', $file->getContent());
    }

    /**
     * write some stuff into stream
     *
     * @test
     */
    public function writeLine()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('home'));
        $file = vfsStream::newFile('test.txt')->at(vfsStreamWrapper::getRoot());
        $resourceOutputStream = new TeststubResourceOutputStream(fopen(vfsStream::url('home/test.txt'), 'w'));
        $this->assertEquals(11, $resourceOutputStream->writeLine('foobarbaz'));
        $this->assertEquals("foobarbaz\r\n", $file->getContent());
    }
}
?>