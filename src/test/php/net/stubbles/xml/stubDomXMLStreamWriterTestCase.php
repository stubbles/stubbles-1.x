<?php
/**
 * Test for net::stubbles::xml::stubDomXMLStreamWriter.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @version     $Id: stubDomXMLStreamWriterTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubDomXMLStreamWriter');
/**
 * Test for net::stubbles::xml::stubDomXMLStreamWriter.
 *
 * @package     stubbles
 * @subpackage  xml_test
 * @group       xml
 */
class stubDomXMLStreamWriterTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Test the creation of an empty document
     *
     * @test
     */
    public function emptyDocument()
    {
        $writer = new stubDomXMLStreamWriter();
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>', $writer->asXML());
        $this->assertEquals('1.0', $writer->getVersion());
        $this->assertEquals('UTF-8', $writer->getEncoding());
        $writer = new stubDomXMLStreamWriter('1.1', 'ISO-8859-1');
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
        $writer = new stubDomXMLStreamWriter();
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
        $writer = new stubDomXMLStreamWriter();
        $writer->writeElement('foo', array('att' => 'value'), 'content');

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="value">content</foo>', $writer->asXML());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function fullElementWithGermanUmlauts()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeElement('foo', array('att' => utf8_decode('hääää')), 'content');

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="hääää">content</foo>', $writer->asXML());
    }

    /**
     * Test creating a document with several tags
     *
     * @test
     */
    public function fullElementWithGermanUmlautsUTF8()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeElement('foo', array('att' => 'hääää'), 'content');

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<foo att="hääää">content</foo>', $writer->asXML());
    }

    /**
     * Test creating a document with attributes
     *
     * @test
     */
    public function attributes()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeAttribute('bar', '42');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="42"/></root>', $writer->asXML());
    }

    /**
     * Test creating a document with attributes
     *
     * @test
     */
    public function attributesWithGermanUmlauts()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeAttribute('bar', utf8_decode('hääää'));
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="hääää"/></root>', $writer->asXML());
    }

    /**
     * Test creating a document with attributes
     *
     * @test
     */
    public function attributesWithGermanUmlautsUTF8()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeStartElement('foo');
        $writer->writeAttribute('bar', 'hääää');
        $writer->writeEndElement();
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="hääää"/></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a text node
     *
     * @test
     */
    public function text()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeText('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text.</root>', $writer->asXML());
    }

    /**
     * Test creating a document with a text node containing german umlauts.
     *
     * @test
     */
    public function textWithGermanUmlauts()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeText(utf8_decode('This is text containing äöü.'));
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text containing äöü.</root>', $writer->asXML());
    }

    /**
     * Test creating a document with a text node containing german umlauts.
     *
     * @test
     */
    public function textWithGermanUmlautsInUTF8()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeText('This is text containing äöü.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root>This is text containing äöü.</root>', $writer->asXML());
    }

    /**
     * Test creating a document with character data
     *
     * @test
     */
    public function cData()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeCData('This is text.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text.]]></root>', $writer->asXML());
    }

    /**
     * Test creating a document with character data
     *
     * @test
     */
    public function cDataWithGermanUmlauts()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeCData(utf8_decode('This is text containing äöü.'));
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text containing äöü.]]></root>', $writer->asXML());
    }

    /**
     * Test creating a document with character data
     *
     * @test
     */
    public function cDataWithGermanUmlautsUTF8()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeCData('This is text containing äöü.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><![CDATA[This is text containing äöü.]]></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a comment
     *
     * @test
     */
    public function comment()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeComment('This is a comment.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment.--></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a comment
     *
     * @test
     */
    public function ommentWithGermanUmlauts()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeComment(utf8_decode('This is a comment containing äöü.'));
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment containing äöü.--></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a comment
     *
     * @test
     */
    public function commentWithGermanUmlautsUTF8()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeComment('This is a comment containing äöü.');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><!--This is a comment containing äöü.--></root>', $writer->asXML());
    }

    /**
     * Test creating a document with a processing instruction
     *
     * @test
     */
    public function processingInstruction()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeProcessingInstruction('php', 'phpinfo();');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><?php phpinfo();?></root>', $writer->asXML());
    }

    /**
     * Test creating a document an XML fragment
     *
     * @test
     */
    public function xmlFragment()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        $writer->writeXmlFragment('<foo bar="true"/>');
        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo bar="true"/></root>', $writer->asXML());
    }

    /**
     * Test importing a stream writer
     *
     * @test
     */
    public function importStreamWriter()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');

        $writer2 = new stubDomXMLStreamWriter();
        $writer2->writeStartElement('foo');
        $writer2->writeStartElement('bar');
        $writer2->writeEndElement();
        $writer2->writeEndElement();

        $writer->importStreamWriter($writer2);

        $writer->writeEndElement();

        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n". '<root><foo><bar/></foo></root>', $writer->asXML());
    }

    /**
     * Test the clear() method
     *
     * @test
     */
    public function clear()
    {
        $writer = new stubDomXMLStreamWriter();
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
        $writer = new stubDomXMLStreamWriter();
        $this->assertTrue($writer->hasFeature(stubXMLStreamWriter::FEATURE_AS_DOM));
        $this->assertTrue($writer->hasFeature(stubXMLStreamWriter::FEATURE_IMPORT_WRITER));
    }

    /**
     * Test writing an invalid fragment
     *
     * @test
     * @expectedException  stubXMLException
     */
    public function fragmentException()
    {
        $writer = new stubDomXMLStreamWriter();
        $writer->writeStartElement('root');
        @$writer->writeXmlFragment('<foo>');
        $writer->writeEndElement();
    }

    /**
     * checks if the finished status is reported properly
     *
     * @test
     */
    public function isFinished()
    {
        $writer = new stubDomXMLStreamWriter();
        $this->assertTrue($writer->isFinished());
        $writer->writeStartElement('root');
        $this->assertFalse($writer->isFinished());
        $writer->writeEndElement();
        $this->assertTrue($writer->isFinished());
    }
}
?>