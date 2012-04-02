<?php
/**
 * Tests for net::stubbles::ipo::request::stubRequestValueError.
 *
 * @package     stubbles
 * @subpackage  ipo_test
 * @version     $Id: stubRequestValueErrorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::stubRequestValueError');
class TestRequestValueErrorCriteria
{
    public function __toString()
    {
        return 'very hypercritical stuff';
    }
}
/**
 * Tests for net::stubbles::ipo::request::stubRequestValueError.
 *
 * @package     stubbles
 * @subpackage  ipo_test
 * @group       ipo
 * @group       ipo_request
 */
class stubRequestValueErrorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test that has no value keys
     *
     * @var  stubRequestValueError
     */
    protected $withValueKeys;
    /**
     * instance to test that has value keys
     *
     * @var  stubRequestValueError
     */
    protected $withoutValueKeys;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $messages = array('en_*'  => 'This is an error with criteria {foo} regarding {bar}.',
                          'de_DE' => 'Dies ist ein Fehler des Kriteriums {foo} betreffend {bar}.'
                    );
        $this->withValueKeys    = new stubRequestValueError('foo', $messages, array('foo', 'bar'));
        $this->withoutValueKeys = new stubRequestValueError('bar', array('en_*' => 'This is an error message.'));
    }

    /**
     * assure that the id is stored and returned correct
     *
     * @test
     */
    public function id()
    {
        $this->assertEquals('foo', $this->withValueKeys->getId());
        $this->assertEquals('bar', $this->withoutValueKeys->getId());
    }

    /**
     * test that checking for existing locale message works correct
     *
     * @test
     */
    public function hasMessages()
    {
        $this->assertTrue($this->withValueKeys->hasMessage('en_*'));
        $this->assertTrue($this->withValueKeys->hasMessage('de_DE'));
        $this->assertFalse($this->withValueKeys->hasMessage('fr_FR'));
        
        $this->assertTrue($this->withoutValueKeys->hasMessage('en_*'));
        $this->assertFalse($this->withoutValueKeys->hasMessage('de_DE'));
        $this->assertFalse($this->withoutValueKeys->hasMessage('fr_FR'));
    }

    /**
     * test that getting a specific locale message works correct
     *
     * @test
     */
    public function getMessage()
    {
        $this->assertEquals('This is an error with criteria  regarding .', $this->withValueKeys->getMessage('en_*'));
        $this->assertEquals('Dies ist ein Fehler des Kriteriums  betreffend .', $this->withValueKeys->getMessage('de_DE'));
        $this->assertNull($this->withValueKeys->getMessage('fr_FR'));
        
        $this->withValueKeys->setValues(array('foo' => 'dummy1', 'bar' => 'dummy2'));
        $this->assertEquals('This is an error with criteria dummy1 regarding dummy2.', $this->withValueKeys->getMessage('en_*'));
        $this->assertEquals('Dies ist ein Fehler des Kriteriums dummy1 betreffend dummy2.', $this->withValueKeys->getMessage('de_DE'));
        $this->assertNull($this->withValueKeys->getMessage('fr_FR'));
        
        $this->withValueKeys->setValues(array('foo' => array('dummy3', 'dummy4'), 'bar' => 'dummy2'));
        $this->assertEquals('This is an error with criteria dummy3, dummy4 regarding dummy2.', $this->withValueKeys->getMessage('en_*'));
        $this->assertEquals('Dies ist ein Fehler des Kriteriums dummy3, dummy4 betreffend dummy2.', $this->withValueKeys->getMessage('de_DE'));
        $this->assertNull($this->withValueKeys->getMessage('fr_FR'));
        
        $this->withValueKeys->setValues(array('foo' => new stdClass(), 'bar' => new TestRequestValueErrorCriteria()));
        $this->assertEquals('This is an error with criteria stdClass regarding very hypercritical stuff.', $this->withValueKeys->getMessage('en_*'));
        $this->assertEquals('Dies ist ein Fehler des Kriteriums stdClass betreffend very hypercritical stuff.', $this->withValueKeys->getMessage('de_DE'));
        $this->assertNull($this->withValueKeys->getMessage('fr_FR'));
        
        $this->assertEquals('This is an error message.', $this->withoutValueKeys->getMessage('en_*'));
        $this->assertNull($this->withoutValueKeys->getMessage('de_DE'));
        $this->assertNull($this->withoutValueKeys->getMessage('fr_FR'));
    }

    /**
     * test that getting all message works correct
     *
     * @test
     */
    public function getMessages()
    {
        $result   = $this->withValueKeys->setValues(array('foo' => 'dummy1', 'bar' => 'dummy2'));
        $messages = $this->withValueKeys->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertEquals('en_*', $messages[0]->getLocale());
        $this->assertEquals('This is an error with criteria dummy1 regarding dummy2.', $messages[0]->getMessage());
        $this->assertEquals('de_DE', $messages[1]->getLocale());
        $this->assertEquals('Dies ist ein Fehler des Kriteriums dummy1 betreffend dummy2.', $messages[1]->getMessage());
        $this->assertSame($this->withValueKeys, $result);

        $result  = $this->withoutValueKeys->setValues(array('foo' => 'dummy1', 'bar' => 'dummy2'));
        $messages = $this->withoutValueKeys->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('en_*', $messages[0]->getLocale());
        $this->assertEquals('This is an error message.', $messages[0]->getMessage());
        $this->assertSame($this->withoutValueKeys, $result);
    }

    /**
     * assure that a missing value triggers an exception
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function setValues()
    {
        $this->withValueKeys->setValues(array('foo' => 'dummy1'));
    }

    /**
     * assure that the value keys are stored and returned correct
     *
     * @test
     */
    public function valueKeys()
    {
        $this->assertEquals(array('foo', 'bar'), $this->withValueKeys->getValueKeys());
        $this->assertEquals(array(), $this->withoutValueKeys->getValueKeys());
    }
}
?>