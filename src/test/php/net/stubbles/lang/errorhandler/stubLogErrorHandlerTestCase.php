<?php
/**
 * Tests for net::stubbles::lang::errorhandler::stubLogErrorHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @version     $Id: stubLogErrorHandlerTestCase.php 3226 2011-11-23 16:14:05Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::errorhandler::stubLogErrorHandler');
@include_once 'vfsStream/vfsStream.php';
/**
 * Tests for net::stubbles::lang::errorhandler::stubLogErrorHandler.
 *
 * @package     stubbles
 * @subpackage  lang_errorhandler_test
 * @group       lang
 * @group       lang_errorhandler
 */
class stubLogErrorHandlerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubLogErrorHandler
     */
    protected $logErrorHandler;
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
            $this->markTestSkipped('stubLogErrorHandlerTestCase::handleErrorShouldLogTheError() requires vfsStream, see http://vfs.bovigo.org/');
        }

        $this->root            = vfsStream::setup();
        $this->logErrorHandler = new stubLogErrorHandler(vfsStream::url('root'));
    }

    /**
     * assure that isResponsible() works correct
     *
     * @test
     */
    public function isResponsible()
    {
        $this->assertTrue($this->logErrorHandler->isResponsible(E_NOTICE, 'foo'));
    }

    /**
     * assure that isSupressable() works correct
     *
     * @test
     */
    public function isSupressable()
    {
        $this->assertFalse($this->logErrorHandler->isSupressable(E_NOTICE, 'foo'));
    }

    /**
     * @test
     */
    public function handleErrorShouldLogTheError()
    {
        $line = __LINE__;
        $this->assertTrue($this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, $line));
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|' . E_WARNING . '|E_WARNING|message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/php-error-' . date('Y-m-d') . '.log')
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
        $this->assertTrue($this->logErrorHandler->handle(E_WARNING, 'message', __FILE__, __LINE__));
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0700, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }

    /**
     * @test
     */
    public function handleErrorShouldLogTheErrorWhenTargetChanged()
    {
        $line = __LINE__;
        $this->assertTrue($this->logErrorHandler->setLogTarget('errors')
                                                ->handle(313, 'message', __FILE__, $line)
        );
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m') . '/errors-' . date('Y-m-d') . '.log'));
        $this->assertEquals('|313|unknown|message|' . __FILE__ . '|' . $line . "\n",
                            substr($this->root->getChild('log/errors/' . date('Y') . '/' . date('m') . '/errors-' . date('Y-m-d') . '.log')
                                       ->getContent(),
                                   19
                            )
        );
    }

    /**
     * @test
     */
    public function handleShouldCreateLogDirectoryWithChangedModeIfNotExists()
    {
        $this->assertTrue($this->logErrorHandler->setMode(0777)->handle(E_WARNING, 'message', __FILE__, __LINE__));
        $this->assertTrue($this->root->hasChild('log/errors/' . date('Y') . '/' . date('m')));
        $this->assertEquals(0777, $this->root->getChild('log/errors/' . date('Y') . '/' . date('m'))->getPermissions());
    }
}
?>