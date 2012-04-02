<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubAbstractExceptionHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubAbstractExceptionHandlerTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubAbstractExceptionHandler',
                      'net::stubbles::lang::exceptions::stubChainedException',
                      'net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Chained exception for test purposes.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 */
class TestAbstractExceptionHandlerException extends stubChainedException
{
    /**
     * returns class name
     *
     * @return  string
     */
    public function getClassName()
    {
        return 'net::stubbles::lang::errorhandler::test::TestAbstractExceptionHandlerException';
    }
}
/**
 * Tests for net::stubbles::lang::errorhandler::stubAbstractExceptionHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubAbstractExceptionHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubAbstractExceptionHandler
     */
    protected $abstractExceptionHandler;
    /**
     * root path for log files
     *
     * @var  stubVfsStreamDirectory
     */
    protected $root;


    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped(__CLASS__ . ' requires vfsStream, see http://vfs.bovigo.org/');
        }

        $this->root                     = vfsStream::setup();
        $this->abstractExceptionHandler = $this->getMock('stubAbstractExceptionHandler', array('fillResponse'), array(vfsStream::url('root')));
    }

    /**
     * @test
     */
    public function loggingDisabledFillsResponseOnly()
    {
        $abstractExceptionHandler = $this->getMock('stubAbstractExceptionHandler', array('fillResponse', 'log'), array(vfsStream::url('root')));
        $abstractExceptionHandler->expects($this->never())->method('log');
        $abstractExceptionHandler->expects($this->once())->method('fillResponse');
        $abstractExceptionHandler->disableLogging()->handleException(new Exception());
    }

    /**
     * @test
     */
    public function handleExceptionLogsExceptionData()
    {
        $this->abstractExceptionHandler->handleException(new Exception('exception message'));
        $line = __LINE__ - 1;

        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|Exception|exception message|' . __FILE__ . '|' . $line . "||||\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );

    }

    /**
     * @test
     */
    public function handleChainedExceptionLogsExceptionDataOfChainedAndCause()
    {
        $exception = new TestAbstractExceptionHandlerException('chained exception', new Exception('exception message'));
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->setLogTarget('foo')->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/foo-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|net::stubbles::lang::errorhandler::test::TestAbstractExceptionHandlerException|chained exception|' . __FILE__ . '|' . $line . '|Exception|exception message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/foo-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );
    }

    /**
     * assure that the exception is logged
     *
     * @test
     */
    public function handleChainedExceptionWithoutChainedException()
    {
        $exception = new TestAbstractExceptionHandlerException('chained exception');
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->handleException($exception);
        $this->assertEquals('|net::stubbles::lang::errorhandler::test::TestAbstractExceptionHandlerException|chained exception|' . __FILE__ . '|' . $line . "||||\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/exceptions-' . date('Y-m-d') . '.log')
                                              ->getContent(),
                                   19
                            )
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithDefaultModeIfNotExists()
    {
        $exception = new TestAbstractExceptionHandlerException('chained exception');
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0700, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedModeIfNotExists()
    {
        $exception = new TestAbstractExceptionHandlerException('chained exception');
        $line      = __LINE__ - 1;

        $this->abstractExceptionHandler->setMode(0777)->handleException($exception);
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0777, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }
}
?>