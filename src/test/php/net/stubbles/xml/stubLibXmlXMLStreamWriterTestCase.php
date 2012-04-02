<?php
/**
 * Test for net::stubbles::xml::stubLibXmlXMLStreamWriter.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @version     $Id: stubLibXmlXMLStreamWriterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubLibXmlXMLStreamWriter');
/**
 * Test for net::stubbles::xml::stubLibXmlXMLStreamWriter.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @group       xml
 */
class stubLibXmlXMLStreamWriterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Test the creation of an empty document
     *
     * @test
     */
    public function emptyDocument()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $writer->asXML());
        $this->assertEquals('1.0', $writer->getVersion());
        $this->assertEquals('UTF-8', $writer->getEncoding());
        $writer = new stubLibXmlXMLStreamWriter('1.1', 'ISO-8859-1');
        $this->assertEquals('1.1', $writer->getVersion());
        $this->assertEquals('ISO-8859-1', $writer->getEncoding());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function tags()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeEndElement();
        $writer->writeStartElement('bar');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n<root><foo/><bar/></root>" , $writer->asXML());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function fullElement()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeElement('foo', array('att' => 'value'), 'content');

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="value">content</foo>', $writer->asXML());
    }

    /**
     * Test creating a document with attributes
     *
     * @test
     */
    public function attributes()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeAttribute('bar', '42');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="42"/></root>' , $writer->asXML());
    }

    /**
     * Test creating a document with a text node
     *
     * @test
     */
    public function text()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeText('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text.</root>' , $writer->asXML());
    }

    /**
     * Test creating a document with character data
     *
     * @test
     */
    public function cData()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeCData('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text.]]></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a comment
     *
     * @test
     */
    public function comment()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeComment('This is a comment.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment.--></root>' , $writer->asXML());
    }

    /**
     * Test creating a document with a processing instruction
     *
     * @test
     */
    public function processingInstruction()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeProcessingInstruction('php', 'phpinfo();');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><?php phpinfo();?></root>' , $writer->asXML());
    }

    /**
     * Test creating a document an XML fragment
     *
     * @test
     */
    public function xmlFragment()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeXmlFragment('<foo bar="true"/>');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="true"/></root>' , $writer->asXML());
    }

    /**
     * Test importing a stream writer
     *
     * @test
     * @expectedException  stubMethodNotSupportedException
     */
    public function importStreamWriter()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeStartElement('root');

        $writer2 = new stubLibXmlXMLStreamWriter();
        $writer2->writeStartElement('foo');
        $writer2->writeStartElement('bar');
        $writer2->writeEndElement();
        $writer2->writeEndElement();

        $writer->importStreamWriter($writer2);
    }
    

    /**
     * Test the clear() method
     *
     * @test
     */
    public function clear()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $writer->writeElement('foo');
        $writer->clear();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $writer->asXML());
    }

    /**
     * Test the hasFeature() method
     *
     * @test
     */
    public function features()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $this->assertTrue($writer->hasFeature(stubXMLStreamWriter::FEATURE_AS_DOM));
        $this->assertFalse($writer->hasFeature(stubXMLStreamWriter::FEATURE_IMPORT_WRITER));
    }

    /**
     * checks if the finished status is reported properly
     *
     * @test
     */
    public function isFinished()
    {
        $writer = new stubLibXmlXMLStreamWriter();
        $this->assertTrue($writer->isFinished());
        $writer->writeStartElement('root');
        $this->assertFalse($writer->isFinished());
        $writer->writeEndElement();
        $this->assertTrue($writer->isFinished());
    }
}
?>