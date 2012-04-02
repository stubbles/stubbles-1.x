<?php
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslMissingIncludeCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @version     $Id: stubXslMissingIncludeCallbackTestCase.php 2972 2011-02-07 18:32:07Z mikey $
 */
stubClassLoader::load('net::stubbles::util::log::appender::stubMemoryLogAppender',
                      'net::stubbles::util::log::entryfactory::stubEmptyLogEntryFactory',
                      'net::stubbles::xml::stubDomXMLStreamWriter',
                      'net::stubbles::xml::xsl::callback::stubXslMissingIncludeCallback'
);
/**
 * Test for net::stubbles::xml::xsl::callback::stubXslMissingIncludeCallback.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback_test
 * @group       xml
 * @group       xml_xsl
 * @group       xml_xsl_callback
 */
class stubXslMissingIncludeCallbackTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXslMissingIncludeCallback
     */
    protected $xslMissingIncludeCallback;
    /**
     * instance to test
     *
     * @var  stubDomXMLStreamWriter
     */
    protected $mockXMLStreamWriter;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockXMLStreamWriter       = new stubDomXMLStreamWriter();
        $this->xslMissingIncludeCallback = new stubXslMissingIncludeCallback($this->mockXMLStreamWriter);
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->xslMissingIncludeCallback->getClass()
                                                          ->getConstructor()
                                                          ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetModeMethod()
    {
        $setModeMethod = $this->xslMissingIncludeCallback->getClass()
                                                         ->getMethod('setMode');
        $this->assertTrue($setModeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setModeMethod->getAnnotation('Inject')->isOptional());
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetLoggerMethod()
    {
        $setLoggerMethod = $this->xslMissingIncludeCallback->getClass()
                                                           ->getMethod('setLogger');
        $this->assertTrue($setLoggerMethod->hasAnnotation('Inject'));
        $this->assertTrue($setLoggerMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setLoggerMethod->hasAnnotation('Named'));
        $this->assertEquals(stubLogger::LEVEL_ERROR,
                            $setLoggerMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function recordMissingIncludeIsAnnotatedAsXslMethod()
    {
        $this->assertTrue($this->xslMissingIncludeCallback->getClass()
                                                          ->getMethod('recordMissingInclude')
                                                          ->hasAnnotation('XslMethod')
        );
    }

    /**
     * @test
     */
    public function createsNodeWithoutModeAndLogger()
    {
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<missing-include/>\n",
                            $this->xslMissingIncludeCallback->recordMissingInclude('file',
                                                                                   'part',
                                                                                   'missing',
                                                                                   'href',
                                                                                   'common'
                                                              )
                                                            ->saveXML()
        );
    }

    /**
     * @test
     */
    public function createsNodeAndLogsInProdMode()
    {
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('PROD'));
        $this->xslMissingIncludeCallback->setMode($mockMode);
        $logger      = new stubLogger(new stubEmptyLogEntryFactory());
        $logAppender = new stubMemoryLogAppender();
        $logger->addLogAppender($logAppender);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<missing-include/>\n",
                            $this->xslMissingIncludeCallback->setLogger($logger)
                                                            ->recordMissingInclude('file',
                                                                                   'part',
                                                                                   'missing',
                                                                                   'href',
                                                                                   'common'
                                                              )
                                                            ->saveXML()
        );
        $this->assertEquals(1, count($logAppender->getLogEntries('missing-includes')));
        $this->assertEquals(array('file',
                                  'part',
                                  'missing',
                                  'href',
                                  'common'
                            ),
                            $logAppender->getLogEntryData('missing-includes', 0)
        );
    }

    /**
     * @test
     */
    public function createsNodeAndLogsInProdModeWithIncompleteData()
    {
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('PROD'));
        $logger      = new stubLogger(new stubEmptyLogEntryFactory());
        $logAppender = new stubMemoryLogAppender();
        $logger->addLogAppender($logAppender);
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<missing-include/>\n",
                            $this->xslMissingIncludeCallback->setMode($mockMode)
                                                            ->setLogger($logger)
                                                            ->recordMissingInclude('file',
                                                                                   'part',
                                                                                   'missing',
                                                                                   '',
                                                                                   ''
                                                              )
                                                            ->saveXML()
        );
        $this->assertEquals(1, count($logAppender->getLogEntries('missing-includes')));
        $this->assertEquals(array('file',
                                  'part',
                                  'missing',
                                  '',
                                  ''
                            ),
                            $logAppender->getLogEntryData('missing-includes', 0)
        );
    }

    /**
     * @test
     * @expectedException  stubXSLProcessorException
     */
    public function throwsXslProcessorExceptionInNonProdMode()
    {
        $mockMode = $this->getMock('stubMode');
        $mockMode->expects($this->once())
                 ->method('name')
                 ->will($this->returnValue('DEV'));
        $this->xslMissingIncludeCallback->setMode($mockMode)
                                        ->recordMissingInclude('file',
                                                               'part',
                                                               'missing',
                                                               'href',
                                                               'project'
                                          );
    }
}
?>