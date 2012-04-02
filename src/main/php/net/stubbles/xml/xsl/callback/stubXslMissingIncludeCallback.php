<?php
/**
 * XSL callback to handle missing include parts.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 * @version     $Id: stubXslMissingIncludeCallback.php 2972 2011-02-07 18:32:07Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::stubMode',
                      'net::stubbles::util::log::stubLogger',
                      'net::stubbles::xml::xsl::stubXSLProcessorException',
                      'net::stubbles::xml::xsl::callback::stubXslAbstractCallback'
);
/**
 * XSL callback to handle missing include parts.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_callback
 */
class stubXslMissingIncludeCallback extends stubXslAbstractCallback
{
    /**
     * runtime mode
     *
     * @var  stubMode
     */
    protected $mode;
    /**
     * logger instance
     *
     * @var  stubLogger
     */
    protected $logger;

    /**
     * sets runtime mode
     *
     * @param   stubMode                       $mode
     * @return  stubXslMissingIncludeCallback
     * @Inject(optional=true)
     */
    public function setMode(stubMode $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * sets runtime mode
     *
     * @param   stubLogger                     $logger
     * @return  stubXslMissingIncludeCallback
     * @Inject(optional=true)
     * @Named(stubLogger::LEVEL_ERROR)
     */
    public function setLogger(stubLogger $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * records missing include part or throws an exception
     *
     * @param   array<DOMAttr>|string  $includingFile  file in which the include happens
     * @param   array<DOMAttr>|string  $includingPart  part which tried the include
     * @param   array<DOMAttr>|string  $includedPart   missing include part
     * @param   array<DOMAttr>|string  $includedFile   file in which the include part should be found
     * @param   array<DOMAttr>|string  $project        project in which file with missing include part should be found
     * @return  DOMDocument
     * @throws  stubXSLProcessorException
     * @XslMethod
     */
    public function recordMissingInclude($includingFile, $includingPart, $includedPart, $includedFile, $project)
    {
        $includingFile = $this->parseValue($includingFile);
        $includingPart = $this->parseValue($includingPart);
        $includedPart  = $this->parseValue($includedPart);
        $includedFile  = $this->parseValue($includedFile);
        $project       = $this->parseValue($project);
        if (null !== $this->mode && $this->mode->name() !== 'PROD') {
            $msg = 'The part "' . $includingPart . '" in file "' . $includingFile . '" can not find the include "' . $includedPart . '"';
            if (empty($includedFile) === false) {
                $msg .= ' in file "' . $includedFile . '"';
                if (empty($project) === false) {
                    $msg .= ' from project "' . $project . '"';
                }
            }
            
            throw new stubXSLProcessorException($msg);
        }
        
        $this->xmlStreamWriter->writeElement('missing-include');
        if (null !== $this->logger) {
            $this->logger->createLogEntry('missing-includes')
                         ->addData($includingFile)
                         ->addData($includingPart)
                         ->addData($includedPart)
                         ->addData($includedFile)
                         ->addData($project)
                         ->log();
        }
        
        return $this->createDomDocument();
    }
}
?>