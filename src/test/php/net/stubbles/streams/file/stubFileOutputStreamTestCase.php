<?php
/**
 * Test for net::stubbles::streams::file::stubFileOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_file_test
 * @version     $Id: stubFileOutputStreamTestCase.php 2336 2009-09-21 21:30:38Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::file::stubFileOutputStream',
                      'net::stubbles::streams::memory::stubMemoryStreamWrapper'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::streams::file::stubFileOutputStream.
 *
 * @package     stubbles
 * @subpackage  streams_file_test
 * @group       streams
 * @group       streams_file
 */
class stubFileOutputStreamTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * the file url used in the tests
     *
     * @var  string
     */
    protected $fileUrl;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Requires vfsStream, see http://vfs.bovigo.org/');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(vfsStream::newDirectory('home'));
        $this->fileUrl = vfsStream::url('home/test.txt');
    }

    /**
     * construct with string as argument
     *
     * @test
     */
    public function constructWithString()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new stubFileOutputStream($this->fileUrl);
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * construct with string as argument and delayed file creation
     *
     * @test
     */
    public function constructWithStringDelayed()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new stubFileOutputStream($this->fileUrl, 'wb', true);
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertTrue(file_exists($this->fileUrl));
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * construct with string as argument fails throws io exception
     *
     * @test
     * @expectedException  stubIOException
     */
    public function constructWithStringFailsAndThrowsIOException()
    {
        $fileOutputStream = new stubFileOutputStream('memory://doesNotExist', 'r');
    }

    /**
     * construct with resource as argument
     *
     * @test
     */
    public function constructWithResource()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = new stubFileOutputStream(fopen($this->fileUrl, 'wb'));
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * construct with an illegal resource as argument
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructWithIllegalResource()
    {
        if (extension_loaded('gd') === false) {
            $this->markTestSkipped('No known extension with other resource type available.');
        }
        
        $fileOutputStream = new stubFileOutputStream(imagecreate(2, 2));
    }

    /**
     * construct with an illegal argument
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function constructWithIllegalArgument()
    {
        $fileOutputStream = new stubFileOutputStream(0);
    }
}
?>