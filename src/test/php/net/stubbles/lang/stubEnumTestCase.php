<?php
/**
 * Tests for net::stubbles::lang::stubEnum.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  lang_test
 */
stubClassLoader::load('net::stubbles::lang::stubEnum');
/**
 * Concrete instance of net::stubbles::lang::stubEnum.
 *
 * @author      Frank Kleine <mikey@stubbles.net>
 * @package     stubbles
 * @subpackage  lang_test
 */
class TeststubEnum extends stubEnum
{
    public static $FOO;
    public static $BAR;
    
    public static function init($values = false)
    {
        if (false === $values) {
            self::$FOO = new self('FOO');
            self::$BAR = new self('BAR');
        } else {
            self::$FOO = new self('FOO', 10);
            self::$BAR = new self('BAR', 20);
        }
    }
    
    public function getClassName()
    {
        return 'net::stubbles::lang::TeststubEnum';
    }
}
/**
 * Tests for net::stubbles::lang::stubEnum.
 *
 * @package     stubbles
 * @subpackage  lang_test
 * @group       lang
 */
class stubEnumTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        TeststubEnum::init();
    }

    /**
     * assure that enums work as expected
     *
     * @test
     */
    public function withoutValues()
    {
        $this->assertEquals('FOO', TeststubEnum::$FOO->name());
        $this->assertEquals('BAR', TeststubEnum::$BAR->name());
        $this->assertEquals('FOO', TeststubEnum::$FOO->value());
        $this->assertEquals('BAR', TeststubEnum::$BAR->value());
        $this->assertTrue(TeststubEnum::$FOO->equals(TeststubEnum::$FOO));
        $this->assertFalse(TeststubEnum::$FOO->equals(TeststubEnum::$BAR));
        $this->assertFalse(TeststubEnum::$BAR->equals(TeststubEnum::$FOO));
        $this->assertFalse(TeststubEnum::$BAR->equals(new stdClass()));
        $this->assertEquals("net::stubbles::lang::TeststubEnum {\n    FOO\n    FOO\n}\n", (string) TeststubEnum::$FOO);
        $this->assertEquals("net::stubbles::lang::TeststubEnum {\n    BAR\n    BAR\n}\n", (string) TeststubEnum::$BAR);
    }

    /**
     * assure that enums work as expected
     *
     * @test
     */
    public function withValues()
    {
        TeststubEnum::init(true);
        $this->assertEquals('FOO', TeststubEnum::$FOO->name());
        $this->assertEquals('BAR', TeststubEnum::$BAR->name());
        $this->assertEquals(10, TeststubEnum::$FOO->value());
        $this->assertEquals(20, TeststubEnum::$BAR->value());
        $this->assertTrue(TeststubEnum::$FOO->equals(TeststubEnum::$FOO));
        $this->assertFalse(TeststubEnum::$FOO->equals(TeststubEnum::$BAR));
        $this->assertFalse(TeststubEnum::$BAR->equals(TeststubEnum::$FOO));
        $this->assertFalse(TeststubEnum::$BAR->equals(new stdClass()));
        $this->assertEquals("net::stubbles::lang::TeststubEnum {\n    FOO\n    10\n}\n", (string) TeststubEnum::$FOO);
        $this->assertEquals("net::stubbles::lang::TeststubEnum {\n    BAR\n    20\n}\n", (string) TeststubEnum::$BAR);
    }

    /**
     * assure that trying to clone throws an exception
     *
     * @test
     * @expectedException  stubRuntimeException
     */
    public function cloningEnumsIsNotAllowed()
    {
        $foo = clone (TeststubEnum::$FOO);
    }

    /**
     * assure that forName() works as expected
     *
     * @test
     */
    public function forName()
    {
        $foo = stubEnum::forName(new ReflectionClass('TeststubEnum'), 'FOO');
        $this->assertSame(TeststubEnum::$FOO, $foo);
        $bar = stubEnum::forName(new ReflectionClass('TeststubEnum'), 'BAR');
        $this->assertSame(TeststubEnum::$BAR, $bar);
        $foo = stubEnum::forName(new ReflectionClass('TeststubEnum'), 'foo');
        $this->assertSame(TeststubEnum::$FOO, $foo);
        $bar = stubEnum::forName(new ReflectionClass('TeststubEnum'), 'bar');
        $this->assertSame(TeststubEnum::$BAR, $bar);
    }

    /**
     * assure that forName() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function forNameNonExistingName()
    {
        stubEnum::forName(new ReflectionClass('TeststubEnum'), 'BAZ');
    }

    /**
     * assure that forName() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function forNameWrongClass()
    {
        stubEnum::forName(new ReflectionClass('stdClass'), 'BAZ');
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     */
    public function forValueWithoutValues()
    {
        $foo = stubEnum::forValue(new ReflectionClass('TeststubEnum'), 'FOO');
        $this->assertSame(TeststubEnum::$FOO, $foo);
        $bar = stubEnum::forValue(new ReflectionClass('TeststubEnum'), 'BAR');
        $this->assertSame(TeststubEnum::$BAR, $bar);
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     */
    public function forValueWithValues()
    {
        TeststubEnum::init(true);
        $foo = stubEnum::forValue(new ReflectionClass('TeststubEnum'), 10);
        $this->assertSame(TeststubEnum::$FOO, $foo);
        $bar = stubEnum::forValue(new ReflectionClass('TeststubEnum'), 20);
        $this->assertSame(TeststubEnum::$BAR, $bar);
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function forValueNonExistingValue()
    {
        stubEnum::forValue(new ReflectionClass('TeststubEnum'), 'BAZ');
    }

    /**
     * assure that forValue() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function forValueWrongClass()
    {
        stubEnum::forValue(new ReflectionClass('stdClass'), 'BAZ');
    }

    /**
     * assure that instances() works as expected
     *
     * @test
     */
    public function instances()
    {
        $this->assertEquals(array(TeststubEnum::$FOO, TeststubEnum::$BAR), stubEnum::instances(new ReflectionClass('TeststubEnum')));
    }

    /**
     * assure that instances() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function instancesWrongClass()
    {
        stubEnum::instances(new ReflectionClass('stdClass'));
    }

    /**
     * assure that namesOf() works as expected
     *
     * @test
     */
    public function namesOf()
    {
        $this->assertEquals(array('FOO', 'BAR'), stubEnum::namesOf(new ReflectionClass('TeststubEnum')));
    }

    /**
     * assure that namesOf() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function namesOfWrongClass()
    {
        stubEnum::namesOf(new ReflectionClass('stdClass'));
    }

    /**
     * assure that instance() works as expected
     *
     * @test
     */
    public function valuesOfWithoutValues()
    {
        $this->assertEquals(array('FOO' => 'FOO', 'BAR' => 'BAR'), stubEnum::valuesOf(new ReflectionClass('TeststubEnum')));
    }

    /**
     * assure that instance() works as expected
     *
     * @test
     */
    public function vluesOfWithValues()
    {
        TeststubEnum::init(true);
        $this->assertEquals(array('FOO' => 10, 'BAR' => 20), stubEnum::valuesOf(new ReflectionClass('TeststubEnum')));
    }

    /**
     * assure that valuesOf() works as expected
     *
     * @test
     * @expectedException  stubIllegalArgumentException
     */
    public function valuesOfWrongClass()
    {
        stubEnum::valuesOf(new ReflectionClass('stdClass'));
    }
}
?>