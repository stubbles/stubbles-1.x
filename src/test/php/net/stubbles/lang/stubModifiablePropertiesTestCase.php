<?php
/**
 * Tests for net::stubbles::lang::stubModifiableProperties.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @version     $Id$
 */
stubClassLoader::load('net::stubbles::lang::stubModifiableProperties');
@include_once 'vfsStream/vfsStream.php';
/**
 * Tests for net::stubbles::lang::stubModifiableProperties.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @since       1.7.0
 * @group       lang
 */
class stubModifiablePropertiesTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubModifiableProperties
     */
    protected $modifiableProperties;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->modifiableProperties = new stubModifiableProperties(array('scalar' => array('stringValue' => 'This is a string',
                                                                                           'intValue'    => '303',
                                                                                           'floatValue'  => '3.13',
                                                                                           'boolValue'   => 'true'
                                                                                     ),
                                                                         'array'  => array('arrayValue'  => 'foo|bar|baz',
                                                                                           'hashValue'   => 'foo:bar|baz',
                                                                                      ),
                                                                         'range'  => array('rangeValue1' => '1..5',
                                                                                           'rangeValue2' => 'a..e'
                                                                                     ),
                                                                         'empty'  => array()
                                                                   )
                                      );
    }

    /**
     * @test
     */
    public function setNonExistingSectionAddsSection()
    {
        $this->assertTrue($this->modifiableProperties->setSection('doesNotExist', array('foo' => 'bar'))
                                                     ->hasSection('doesNotExist')
        );
        $this->assertEquals(array('foo' => 'bar'),
                            $this->modifiableProperties->getSection('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setExistingSectionReplacesSection()
    {
        $this->assertEquals(array('foo' => 'bar'),
                            $this->modifiableProperties->setSection('empty', array('foo' => 'bar'))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForNonExistingSectionAddsSectionAndValue()
    {
        $this->assertTrue($this->modifiableProperties->setValue('doesNotExist', 'foo', 'bar')
                                                     ->hasSection('doesNotExist')
        );
        $this->assertEquals(array('foo' => 'bar'),
                            $this->modifiableProperties->getSection('doesNotExist')
        );
    }

    /**
     * @test
     */
    public function setNonExistingValueForExistingSectionAddsValueToSection()
    {
        $this->assertEquals(array('stringValue' => 'bar',
                                  'intValue'    => '303',
                                  'floatValue'  => '3.13',
                                  'boolValue'   => 'true'
                            ),
                            $this->modifiableProperties->setValue('scalar', 'stringValue', 'bar')
                                                       ->getSection('scalar')
        );
    }

    /**
     * @test
     */
    public function setExistingValueForExistingSectionReplacesValueInSection()
    {
        $this->assertEquals(array('foo' => 'bar'),
                            $this->modifiableProperties->setValue('empty', 'foo', 'bar')
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setBooleanTrueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => 'true'),
                            $this->modifiableProperties->setBooleanValue('empty', 'foo', true)
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setBooleanFalseTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => 'false'),
                            $this->modifiableProperties->setBooleanValue('empty', 'foo', false)
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setArrayValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => '1|2|3'),
                            $this->modifiableProperties->setArrayValue('empty', 'foo', array(1, 2, 3))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setHashValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => '1:10|2:20|3:30'),
                            $this->modifiableProperties->setHashValue('empty', 'foo', array(1 => 10, 2 => 20, 3 => 30))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setIntegerRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => '1..5'),
                            $this->modifiableProperties->setRangeValue('empty', 'foo', array(1, 2, 3, 4, 5))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setReverseIntegerRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => '5..1'),
                            $this->modifiableProperties->setRangeValue('empty', 'foo', array(5, 4, 3, 2, 1))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setCharacterRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => 'a..e'),
                            $this->modifiableProperties->setRangeValue('empty', 'foo', array('a', 'b', 'c', 'd', 'e'))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     */
    public function setReverseCharacterRangeValueTransformsToPropertyStorage()
    {
        $this->assertEquals(array('foo' => 'e..a'),
                            $this->modifiableProperties->setRangeValue('empty', 'foo', array('e', 'd', 'c', 'b', 'a'))
                                                       ->getSection('empty')
        );
    }

    /**
     * @test
     * @expectedException  stubFileNotFoundException
     */
    public function fromNonExistantFileThrowsException()
    {
        stubModifiableProperties::fromFile(dirname(__FILE__) . '/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  stubIOException
     */
    public function invalidIniFileThrowsException()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Test for feed loading required vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('config'));
        vfsStream::newFile('invalid.ini')
                 ->at(vfsStreamWrapper::getRoot())
                 ->withContent("[invalid{");
        stubModifiableProperties::fromFile(vfsStream::url('config/invalid.ini'));
    }

    /**
     * @test
     */
    public function validIniFileReturnsInstance()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped('Test for feed loading required vfsStream, see http://vfs.bovigo.org/.');
        }
        
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('config'));
        vfsStream::newFile('test.ini')
                 ->at(vfsStreamWrapper::getRoot())
                 ->withContent("[foo]\nbar=baz");
        $properties = stubModifiableProperties::fromFile(vfsStream::url('config/test.ini'));
        $this->assertInstanceOf('stubProperties', $properties);
        $this->assertTrue($properties->hasSection('foo'));
        $this->assertEquals(array('bar' => 'baz'), $properties->getSection('foo'));
    }
}
?>