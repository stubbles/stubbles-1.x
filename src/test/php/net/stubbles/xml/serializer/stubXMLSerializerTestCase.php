<?php
/**
 * Test for net::stubbles::xml::serializer::stubXMLSerializer.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_test
 * @version     $Id: stubXMLSerializerTestCase.php 3090 2011-03-15 20:51:44Z mikey $
 */
stubClassLoader::load('net::stubbles::xml::stubDomXMLStreamWriter',
                      'net::stubbles::xml::serializer::stubXMLSerializer',
                      'org::stubbles::test::xml::serializer::ContainerWithArrayListTagName',
                      'org::stubbles::test::xml::serializer::ContainerWithArrayListWithoutTagName',
                      'org::stubbles::test::xml::serializer::ContainerWithIterator',
                      'org::stubbles::test::xml::serializer::ExampleObjectClass',
                      'org::stubbles::test::xml::serializer::ExampleObjectClassWithEmptyAttributes',
                      'org::stubbles::test::xml::serializer::ExampleObjectClassWithMethods',
                      'org::stubbles::test::xml::serializer::ExampleObjectClassWithSerializer',
                      'org::stubbles::test::xml::serializer::ExampleObjectSerializer',
                      'org::stubbles::test::xml::serializer::ExampleObjectWithInvalidXmlFragments',
                      'org::stubbles::test::xml::serializer::ExampleObjectWithUmlauts',
                      'org::stubbles::test::xml::serializer::ExampleObjectWithXmlFragments',
                      'org::stubbles::test::xml::serializer::ExampleStaticClass'
);
/**
 * Test for net::stubbles::xml::serializer::stubXMLSerializer.
 *
 * @package     stubbles
 * @subpackage  xml_serializer_test
 * @group       xml
 * @group       xml_serializer
 */
class stubXMLSerializerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * The XMLSerializer to use
     *
     * @var stubXMLSerializer
     */
    protected $serializer;
    /**
     * mocked injector instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockInjector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        libxml_clear_errors();
        $this->mockInjector = $this->getMock('stubInjector');
        $this->serializer   = new stubXMLSerializer($this->mockInjector);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        libxml_clear_errors();
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->serializer->getClass()
                                           ->getConstructor()
                                           ->hasAnnotation('Inject')
        );
    }

    /**
     *
     * @param   mixed   $value
     * @param   string  $tagName         optional  name of the surrounding xml tag
     * @param   string  $elementTagName  optional  recurring element tag name for lists
     * @return  string
     */
    protected function serialize($value, $tagName = null, $elementTagName = null)
    {
        return $this->serializer->serialize($value, new stubDomXMLStreamWriter(), $tagName, $elementTagName)
                                ->asXML();
    }

    /**
     * adds prefix to given xml string
     *
     * @param   string  $xml
     * @return  string
     */
    protected function getXmlWithPrefix($xml)
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $xml;
    }

    /**
     * @test
     */
    public function serializeNullWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<null><null/></null>'),
                            $this->serialize(null)
        );
    }

    /**
     * @test
     */
    public function serializeNullWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><null/></root>'),
                            $this->serialize(null, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<boolean>true</boolean>'),
                            $this->serialize(true)
        );
    }

    /**
     * @test
     */
    public function serializeBooleanTrueWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root>true</root>'),
                            $this->serialize(true, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<boolean>false</boolean>'),
                            $this->serialize(false)
        );
    }

    /**
     * @test
     */
    public function serializeBooleanFalseWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root>false</root>'),
                            $this->serialize(false, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeStringWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<string>This is a string.</string>'),
                            $this->serialize('This is a string.')
        );
    }

    /**
     * @test
     */
    public function serializeStringWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root>This is a string.</root>'),
                            $this->serialize('This is a string.', 'root')
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<integer>45</integer>'),
                            $this->serialize(45)
        );
    }

    /**
     * @test
     */
    public function serializeIntegerWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root>45</root>'),
                            $this->serialize(45, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<double>2.352</double>'),
                            $this->serialize(2.352)
        );
    }

    /**
     * @test
     */
    public function serializeFloatWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root>2.352</root>'),
                            $this->serialize(2.352, 'root')
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<array><one>two</one><three>four</three></array>'),
                            $this->serialize(array('one'   => 'two',
                                                   'three' => 'four'
                                             )
                            )
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeArrayWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><one>two</one><three>four</three></root>'),
                            $this->serialize(array('one'   => 'two',
                                                   'three' => 'four'
                                             ),
                                             'root'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<array><string>one</string><integer>2</integer><string>three</string></array>'),
                            $this->serialize(array('one', 2, 'three'))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><string>one</string><integer>2</integer><string>three</string></root>'),
                            $this->serialize(array('one', 2, 'three'), 'root')
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithoutTagNameAndGivenElementTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<array><foo>one</foo><foo>2</foo><foo>three</foo></array>'),
                            $this->serialize(array('one', 2, 'three'), null, 'foo')
        );
    }

    /**
     * @test
     */
    public function serializeIndexedArrayWithGivenTagNameAndElementTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'),
                            $this->serialize(array('one', 2, 'three'), 'root', 'foo')
        );
    }

    /**
     * @test
     */
    public function serializeNestedArray()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><one>two</one><three><four>five</four></three></root>'),
                            $this->serialize(array('one'   => 'two',
                                                   'three' => array('four' => 'five')
                                             ),
                                             'root'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<array><one>two</one><three>four</three></array>'),
                            $this->serialize(new ArrayIterator(array('one'   => 'two',
                                                                     'three' => 'four'
                                                               )
                                             )
                            )
        );
    }

    /**
     * @test
     */
    public function serializeAssociativeIteratorWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><one>two</one><three>four</three></root>'),
                            $this->serialize(new ArrayIterator(array('one'   => 'two',
                                                                     'three' => 'four'
                                                               )
                                             ),
                                             'root'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<array><string>one</string><integer>2</integer><string>three</string></array>'),
                            $this->serialize(new ArrayIterator(array('one', 2, 'three')))
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><string>one</string><integer>2</integer><string>three</string></root>'),
                            $this->serialize(new ArrayIterator(array('one', 2, 'three')),
                                             'root'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeIndexedIteratorWithGivenTagNameAndElementTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><foo>one</foo><foo>2</foo><foo>three</foo></root>'),
                            $this->serialize(new ArrayIterator(array('one', 2, 'three')),
                                             'root',
                                             'foo'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeNestedIterator()
    {
        $this->assertEquals($this->getXmlWithPrefix('<root><one>two</one><three><four>five</four></three></root>'),
                            $this->serialize(new ArrayIterator(array('one'   => 'two',
                                                                     'three' => new ArrayIterator(array('four' => 'five'))
                                                               )
                                             ),
                                             'root'
                            )
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithoutTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<foo bar="test"><bar>42</bar></foo>'),
                            $this->serialize(new ExampleObjectClass())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithGivenTagName()
    {
        $this->assertEquals($this->getXmlWithPrefix('<baz bar="test"><bar>42</bar></baz>'),
                            $this->serialize(new ExampleObjectClass(), 'baz')
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlSerializerAnnotation()
    {
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->with($this->equalTo('org::stubbles::test::xml::serializer::ExampleObjectSerializer'))
                           ->will($this->returnValue(new ExampleObjectSerializer()));
        $this->assertEquals($this->getXmlWithPrefix('<example sound="303"><anything>something</anything></example>'),
                            $this->serialize(new ExampleObjectClassWithSerializer())
        );
    }

    /**
     * @test
     */
    public function serializeNestedObject()
    {
        $obj      = new ExampleObjectClass();
        $obj->bar = new ExampleObjectClass();
        $this->assertEquals($this->getXmlWithPrefix('<foo bar="test"><bar bar="test"><bar>42</bar></bar></foo>'),
                            $this->serialize($obj)
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArray()
    {
        $this->assertEquals($this->getXmlWithPrefix('<container><list><item>one</item><item>two</item><item>three</item></list></container>'),
                            $this->serialize(new ContainerWithArrayListTagName())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsArrayWhereArrayTagNameIsDisabled()
    {
        $this->assertEquals($this->getXmlWithPrefix('<container><item>one</item><item>two</item><item>three</item></container>'),
                            $this->serialize(new ContainerWithArrayListWithoutTagName())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWhichContainsIterator()
    {
        $this->assertEquals($this->getXmlWithPrefix('<container><item>one</item><item>two</item><item>three</item></container>'),
                            $this->serialize(new ContainerWithIterator())
        );
    }

    /**
     * @test
     */
    public function serializeStandardObject()
    {
        $this->assertEquals($this->getXmlWithPrefix('<class method="returned" isFoo="true" isBar="false"/>'),
                            $this->serialize(new ExampleObjectClassWithMethods())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithXmlFragment()
    {
        $this->assertEquals($this->getXmlWithPrefix('<test><xml><foo>bar</foo></xml><foo>bar</foo><description>foo<br/>' . "\n" . 'b&amp;ar<br/>' . "\n" . '<br/>' . "\n" . 'baz</description></test>'),
                            $this->serialize(new ExampleObjectWithXmlFragments())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithInvalidXmlFragment()
    {
        $this->assertEquals($this->getXmlWithPrefix('<test><noXml>bar</noXml><noData/></test>'),
                            $this->serialize(new ExampleObjectWithInvalidXmlFragments())
        );
    }

    /**
     * @test
     */
    public function serializeObjectWithEmptyAttributes()
    {
        $this->assertEquals($this->getXmlWithPrefix('<test emptyProp2="" emptyMethod2=""/>'),
                            $this->serialize(new ExampleObjectClassWithEmptyAttributes())
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeStaticPropertiesAndMethods()
    {
        $this->assertEquals($this->getXmlWithPrefix('<ExampleStaticClass/>'),
                            $this->serialize(new ExampleStaticClass())
        );
    }

    /**
     * @test
     */
    public function serializeObjectContainingUmlauts()
    {
        $this->assertEquals($this->getXmlWithPrefix('<test bar="Hähnchen"><foo>Hähnchen</foo></test>'),
                            $this->serialize(new ExampleObjectWithUmlauts())
        );
    }

    /**
     * @test
     */
    public function doesNotSerializeResources()
    {
        $fp = fopen(__FILE__, 'rb');
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>',
                            $this->serialize($fp)
        );
        fclose($fp);
    }
}
?>