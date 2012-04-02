<?php
/**.
 * Test for net::stubbles::xml::unserializer::stubXMLUnserializer.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer_test
 * @version     $Id: stubXMLUnserializerTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::unserializer::stubXMLUnserializer');

/**
 * Test for net::stubbles::xml::unserializer::stubXMLUnserializer.
 *
 * @package     stubbles
 * @subpackage  xml_unserializer_test
 * @group       xml
 * @group       xml_unserializer
 */
class stubXMLUnserializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test unserializing any XML
     *
     * @test
     */
    public function unserializeAnyXML()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>' . 
               '<users>' .
               '  <user handle="schst">Stephan Schmidt</user>' .
               '  <user handle="mikey">Frank Kleine</user>' .
               '  <group name="dev">Stubbles Development Team</group>' .
               '  <foo id="test">This is handled by the default keyAttribute</foo>' .
               '  <foo id="test2">Another foo tag</foo>' .
               '</users>';
        $options      = array(stubXMLUnserializerOption::ATTRIBUTE_KEY => array('user'     => 'handle',
                                                                                'group'    => 'name',
                                                                                '#default' => 'id'
                                                                          )
                        );
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('schst' => 'Stephan Schmidt',
                                  'mikey' => 'Frank Kleine',
                                  'dev'   => 'Stubbles Development Team',
                                  'test'  => 'This is handled by the default keyAttribute',
                                  'test2' => 'Another foo tag'
                            ),
                            $unserializer->unserialize($xml)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeList()
    {
        $xml1 = '<?xml version="1.0" encoding="iso-8859-1"?>
                <root>
                   <item>
                     <name>schst</name>
                   </item>
                   <item>
                     <name>mikey</name>
                   </item>
                 </root>';
                    
        $xml2 = '<?xml version="1.0" encoding="iso-8859-1"?>
                <root>
                   <item>
                     <name>schst</name>
                   </item>
                 </root>';
        $options      = array(stubXMLUnserializerOption::FORCE_LIST => array('item'));
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('item' => array(array('name' => 'schst'),
                                                  array('name' => 'mikey')
                                            )
                            ),
                            $unserializer->unserialize($xml1)
        );
        $this->assertEquals(array('item' => array(array('name' => 'schst'))), $unserializer->unserialize($xml2));
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceTrim()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>
                   
                    This XML
                    document
                    contains
                    line breaks.
                
                  </string>
                </xml>';
        $options      = array(stubXMLUnserializerOption::WHITESPACE => stubXMLUnserializerOption::WHITESPACE_TRIM);
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('string' => 'This XML
                    document
                    contains
                    line breaks.'),
                            $unserializer->unserialize($xml)
        );
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceNormalize()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>
                   
                    This XML
                    document
                    contains
                    line breaks.
                
                  </string>
                </xml>';
        $options      = array(stubXMLUnserializerOption::WHITESPACE => stubXMLUnserializerOption::WHITESPACE_NORMALIZE);
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('string' => 'This XML document contains line breaks.'), $unserializer->unserialize($xml));
    }

    /**
     * test that whitespace handling works as expected
     *
     * @test
     */
    public function whiteSpaceKeep()
    {
        $xml = '<?xml version="1.0" encoding="iso-8859-1"?>
                <xml>
                  <string>
                   
                    This XML
                    document
                    contains
                    line breaks.
                
                  </string>
                </xml>';
        $options      = array(stubXMLUnserializerOption::WHITESPACE => stubXMLUnserializerOption::WHITESPACE_KEEP);
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('string' => '
                   
                    This XML
                    document
                    contains
                    line breaks.
                
                  '),
                           $unserializer->unserialize($xml)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithAttributes()
    {
        $options      = array(stubXMLUnserializerOption::ATTRIBUTES_PARSE    => true,
                              stubXMLUnserializerOption::ATTRIBUTES_ARRAYKEY => false
                        );
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('test' => array('foo'      => 'bar',
                                                  'tag'      => 'test',
                                                  '_content' => 'Test'
                                            )
                            ),
                            $unserializer->unserializeFile(TEST_SRC_PATH . '/resources/unserializer.xml')
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithTagMap()
    {
        $xml1         = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <foo>FOO</foo>' .
                        '  <bar>BAR</bar>' .
                        '</root>';
        $xml2         = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <foo>'.
                        '    <tomato>45</tomato>'.
                        '  </foo>'.
                        '  <bar>'.
                        '    <tomato>31</tomato>'.
                        '  </bar>'.
                        '</root>';
        $options      = array(stubXMLUnserializerOption::TAG_MAP => array('foo' => 'bar',
                                                                          'bar' => 'foo'
                                                                    )
                        );
        $unserializer = new stubXMLUnserializer($options);
        $this->assertEquals(array('bar' => 'FOO',
                                  'foo' => 'BAR'
                            ),
                            $unserializer->unserialize($xml1)
        );
        $this->assertEquals(array('bar' => array('tomato' => 45),
                                  'foo' => array('tomato' => 31)
                            ),
                            $unserializer->unserialize($xml2)
        );
    }

    /**
     * test unserializing a list of items
     *
     * @test
     */
    public function unserializeWithTypeGuessing()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?>' .
                        '<root>' .
                        '  <string>Just a string...</string>' .
                        '  <booleanValue>true</booleanValue>' .
                        '  <foo>-563</foo>' .
                        '  <bar>4.73736</bar>' .
                        '  <array foo="false" bar="12">true</array>' .
                        '</root>';
        $options      = array(stubXMLUnserializerOption::ATTRIBUTES_PARSE => true,
                              stubXMLUnserializerOption::GUESS_TYPES      => true
                        );
        $unserializer = new stubXMLUnserializer($options);
        $result       = $unserializer->unserialize($xml);
        $this->assertEquals(array('string'       => 'Just a string...',
                                  'booleanValue' => true,
                                  'foo'          => -563,
                                  'bar'          => 4.73736,
                                  'array'        => array('foo'      => false,
                                                          'bar'      => 12,
                                                          '_content' => true
                                                    )
                             ),
                             $result
        );
        $this->assertTrue($result['booleanValue']);
        $this->assertTrue(is_int($result['foo']));
        $this->assertTrue(is_float($result['bar']));
        $this->assertFalse($result['array']['foo']);
        $this->assertTrue(is_int($result['array']['bar']));
        $this->assertTrue($result['array']['_content']);
    }

    /**
     * assert that output encoding is UTF-8
     *
     * @test
     */
    public function returnEncoding()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?><root><string>A string containing german umlauts: ���</string></root>';
        $unserializer = new stubXMLUnserializer();
        $this->assertEquals(array('string' => utf8_encode('A string containing german umlauts: ���')), $unserializer->unserialize($xml));
    }

    /**
     * assert that cdata is supported
     *
     * @test
     */
    public function cDATA()
    {
        $xml          = '<?xml version="1.0" encoding="iso-8859-1"?><root><string><![CDATA[A string containing german umlauts: &���]]></string></root>';
        $unserializer = new stubXMLUnserializer();
        $this->assertEquals(array('string' => utf8_encode('A string containing german umlauts: &���')), $unserializer->unserialize($xml));
    }

    /**
     * test unserializing a non-existing file
     *
     * @test
     * @expectedException  stubFileNotFoundException
     */
    public function unserializeNonExistingFile()
    {
        $unserializer = new stubXMLUnserializer();
        $unserializer->unserializeFile(TEST_SRC_PATH . '/resources/doesNotExist.xml');
    }
}
?>