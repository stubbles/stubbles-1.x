<?php
/**
 * Tests for net::stubbles::ipo::request::filter::stubLengthFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @version     $Id: stubLengthFilterDecoratorTestCase.php 2857 2011-01-10 13:43:39Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::request::filter::stubLengthFilterDecorator');
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 */
class TeststubLengthFilterDecorator extends stubLengthFilterDecorator
{
    /**
     * helper method for direct access to protected doExecute()
     *
     * @param   mixed  $value
     * @return  mixed
     */
    public function callDoExecute($value)
    {
        return $this->doExecute($value);
    }
}
/**
 * Tests for net::stubbles::ipo::request::filter::stubLengthFilterDecorator.
 *
 * @package     stubbles
 * @subpackage  ipo_request_filter_test
 * @group       ipo
 * @group       ipo_request
 * @group       ipo_request_filter
 */
class stubLengthFilterDecoratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * a mock to be used for the rveFactory
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRequestValueErrorFactory;
    /**
     * the instance to test
     *
     * @var  TeststubLengthFilterDecorator
     */
    protected $lengthFilterDecorator;

    /**
     * create test environment
     */
    public function setUp()
    {
        $this->mockRequestValueErrorFactory = $this->getMock('stubRequestValueErrorFactory');
        $this->lengthFilterDecorator        = new TeststubLengthFilterDecorator($this->getMock('stubFilter'), $this->mockRequestValueErrorFactory);
    }

    /**
     * setting no length validators will just pass the value
     *
     * @test
     */
    public function noLengthValidatorsSet()
    {
        $this->assertEquals('minLengthTest', $this->lengthFilterDecorator->callDoExecute("minLengthTest"));
        $this->assertNull($this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertNull($this->lengthFilterDecorator->getMaxLengthValidator());
    }

    /**
     * test handling of empty values
     *
     * @test
     */
    public function emptyValues()
    {
        $mockMinLengthValidaror = $this->getMock('stubValidator');
        $mockMinLengthValidaror->expects($this->never())
                               ->method('validate');
        $this->lengthFilterDecorator->setMinLengthValidator($mockMinLengthValidaror);
        $mockMaxLengthValidaror = $this->getMock('stubValidator');
        $mockMaxLengthValidaror->expects($this->never())
                               ->method('validate');
        $this->lengthFilterDecorator->setMaxLengthValidator($mockMaxLengthValidaror);
        $this->assertNull($this->lengthFilterDecorator->callDoExecute(null));
        $this->assertNull($this->lengthFilterDecorator->callDoExecute(''));
    }

    /**
     * test handling of non-empty values
     *
     * @test
     */
    public function nonEmptyValues()
    {
        $mockMinLengthValidaror = $this->getMock('stubValidator');
        $mockMinLengthValidaror->expects($this->exactly(2))
                               ->method('validate')
                               ->will($this->returnValue(true));
        $this->lengthFilterDecorator->setMinLengthValidator($mockMinLengthValidaror);
        $mockMaxLengthValidaror = $this->getMock('stubValidator');
        $mockMaxLengthValidaror->expects($this->exactly(2))
                               ->method('validate')
                               ->will($this->returnValue(true));
        $this->lengthFilterDecorator->setMaxLengthValidator($mockMaxLengthValidaror);
        $this->assertEquals(0, $this->lengthFilterDecorator->callDoExecute(0));
        $this->assertEquals('0', $this->lengthFilterDecorator->callDoExecute('0'));
    }

    /**
     * assure that filtering a string with minimum length is ok and a string
     * with length shorter than minimum length throws an FilterException
     *
     * @test
     */
    public function minLength()
    {
        $mockMinLengthValidaror = $this->getMock('stubValidator');
        $mockMinLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('minLengthTest'))
                               ->will($this->returnValue(true));
        $this->lengthFilterDecorator->setMinLengthValidator($mockMinLengthValidaror);
        $this->assertSame($mockMinLengthValidaror, $this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertNull($this->lengthFilterDecorator->getMaxLengthValidator());
        $this->assertEquals('minLengthTest', $this->lengthFilterDecorator->callDoExecute("minLengthTest"));
    }

    /**
     * assure that filtering a string with minimum length is ok and a string
     * with length shorter than minimum length throws an FilterException
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function minLengthFails()
    {
        $mockMinLengthValidaror = $this->getMock('stubValidator');
        $mockMinLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('minLengthTest'))
                               ->will($this->returnValue(false));
        $mockMinLengthValidaror->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->lengthFilterDecorator->setMinLengthValidator($mockMinLengthValidaror);
        $this->assertEquals('STRING_TOO_SHORT', $this->lengthFilterDecorator->getMinLengthErrorId());
        $this->assertSame($mockMinLengthValidaror, $this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertNull($this->lengthFilterDecorator->getMaxLengthValidator());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('STRING_TOO_SHORT'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->lengthFilterDecorator->callDoExecute("minLengthTest");
    }

    /**
     * assure that filtering a string with minimum length is ok and a string
     * with length shorter than minimum length throws an FilterException
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function minLengthFailsDifferentErrorId()
    {
        $mockMinLengthValidaror = $this->getMock('stubValidator');
        $mockMinLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('minLengthTest'))
                               ->will($this->returnValue(false));
        $mockMinLengthValidaror->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->lengthFilterDecorator->setMinLengthValidator($mockMinLengthValidaror, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->lengthFilterDecorator->getMinLengthErrorId());
        $this->assertSame($mockMinLengthValidaror, $this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertNull($this->lengthFilterDecorator->getMaxLengthValidator());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->lengthFilterDecorator->callDoExecute("minLengthTest");
    }

    /**
     * assure that filtering a string with maximum length is ok and a string
     * with length greater than maximum length throws an FilterException
     *
     * @test
     */
    public function maxLength()
    {
        $mockMaxLengthValidaror = $this->getMock('stubValidator');
        $mockMaxLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('maxLengthTest'))
                               ->will($this->returnValue(true));
        $this->lengthFilterDecorator->setMaxLengthValidator($mockMaxLengthValidaror);
        $this->assertNull($this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertSame($mockMaxLengthValidaror, $this->lengthFilterDecorator->getMaxLengthValidator());
        $this->assertEquals('maxLengthTest', $this->lengthFilterDecorator->callDoExecute("maxLengthTest"));
    }

    /**
     * assure that filtering a string with maximum length is ok and a string
     * with length greater than maximum length throws an FilterException
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function maxLengthFails()
    {
        $mockMaxLengthValidaror = $this->getMock('stubValidator');
        $mockMaxLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('maxLengthTest'))
                               ->will($this->returnValue(false));
        $mockMaxLengthValidaror->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->lengthFilterDecorator->setMaxLengthValidator($mockMaxLengthValidaror);
        $this->assertEquals('STRING_TOO_LONG', $this->lengthFilterDecorator->getMaxLengthErrorId());
        $this->assertNull($this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertSame($mockMaxLengthValidaror, $this->lengthFilterDecorator->getMaxLengthValidator());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('STRING_TOO_LONG'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->lengthFilterDecorator->callDoExecute("maxLengthTest");
    }

    /**
     * assure that filtering a string with maximum length is ok and a string
     * with length greater than maximum length throws an FilterException
     *
     * @test
     * @expectedException  stubFilterException
     */
    public function maxLengthFailsDifferentErrorId()
    {
        $mockMaxLengthValidaror = $this->getMock('stubValidator');
        $mockMaxLengthValidaror->expects($this->once())
                               ->method('validate')
                               ->with($this->equalTo('maxLengthTest'))
                               ->will($this->returnValue(false));
        $mockMaxLengthValidaror->expects($this->once())
                               ->method('getCriteria')
                               ->will($this->returnValue(array()));
        $this->lengthFilterDecorator->setMaxLengthValidator($mockMaxLengthValidaror, 'differentErrorId');
        $this->assertEquals('differentErrorId', $this->lengthFilterDecorator->getMaxLengthErrorId());
        $this->assertNull($this->lengthFilterDecorator->getMinLengthValidator());
        $this->assertSame($mockMaxLengthValidaror, $this->lengthFilterDecorator->getMaxLengthValidator());
        $this->mockRequestValueErrorFactory->expects($this->once())
                                           ->method('create')
                                           ->with($this->equalTo('differentErrorId'))
                                           ->will($this->returnValue(new stubRequestValueError('foo', array('en_EN' => 'Something wrent wrong.'))));
        $this->lengthFilterDecorator->callDoExecute("maxLengthTest");
    }
}
?>