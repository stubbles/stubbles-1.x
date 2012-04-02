<?php
/**
 * Test for net::stubbles::streams::file::stubFileStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_file_test
 * @version     $Id: stubFileStreamFactoryTestCase.php 2971 2011-02-07 18:24:48Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::file::stubFileStreamFactory');
@include_once 'vfsStream/vfsStream.php';
/**
 * Test for net::stubbles::streams::file::stubFileStreamFactory.
 *
 * @package     stubbles
 * @subpackage  streams_file_test
 * @group       streams
 * @group       streams_file
 */
class stubFileStreamFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubFileStreamFactory
     */
    protected $fileStreamFactory;
    /**
     * a file url used in the tests
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
        vfsStream::newFile('in.txt')->at(vfsStreamWrapper::getRoot())->withContent('foo');
        $this->fileUrl           = vfsStream::url('home/out.txt');
        $this->fileUrl2          = vfsStream::url('home/test/out.txt');
        $this->fileStreamFactory = new stubFileStreamFactory();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $constructor = $this->fileStreamFactory->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->getAnnotation('Inject')->isOptional());
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.filemode', $constructor->getAnnotation('Named')->getName());
    }

    /**
     * @test
     */
    public function createInputStreamWithOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(vfsStream::url('home/in.txt'), array('filemode' => 'rb'));
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function createInputStreamWithoutOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(vfsStream::url('home/in.txt'));
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOption()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('filemode' => 'wb'));
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOptionAndDirectoryOptionSetToTrue()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('filemode'             => 'wb',
                                                                                               'createDirIfNotExists' => true
                                                                                         )
                                                      );
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     * @expectedException  stubIOException
     */
    public function createOutputStreamWithDirectoryOptionNotSetThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2);
    }

    /**
     * @test
     * @expectedException  stubIOException
     */
    public function createOutputStreamWithDirectoryOptionSetToFalseThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => false));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithDefaultPermissions()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => true));
        $this->assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl2));
        $this->assertEquals(0700, vfsStreamWrapper::getRoot()->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithOptionsPermissions()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => true,
                                                                                                'dirPermissions'       => 0666
                                                                                          )
                                                      );
        $this->assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl2));
        $this->assertEquals(0666, vfsStreamWrapper::getRoot()->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createOutputStreamWithDelayedOption()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('delayed' => true));
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertTrue(file_exists($this->fileUrl));
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     */
    public function createOutputStreamWithoutOptions()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl);
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }
}
?>