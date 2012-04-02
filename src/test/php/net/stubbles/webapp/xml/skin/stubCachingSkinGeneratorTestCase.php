<?php
/**
 * Tests for net::stubbles::webapp::xml::skin::stubCachingSkinGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin_test
 * @version     $Id: stubCachingSkinGeneratorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::xml::skin::stubCachingSkinGenerator');
/**
 * Tests for net::stubbles::webapp::xml::skin::stubCachingSkinGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_skin_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_skin
 */
class stubCachingSkinGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubCachingSkinGenerator
     */
    protected $cachingSkinGenerator;
    /**
     * mocked decorated skin generator instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSkinGenerator;
    /**
     * mocked cache container instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheContainer;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockSkinGenerator    = $this->getMock('stubSkinGenerator');
        $this->mockCacheContainer   = $this->getMock('stubCacheContainer');
        $this->cachingSkinGenerator = new stubCachingSkinGenerator($this->mockSkinGenerator, $this->mockCacheContainer);
    }

    /**
     * annotations should be present
     *
     * @test
     */
    public function annotationsPresent()
    {
        $refConstructor = $this->cachingSkinGenerator->getClass()->getConstructor();
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));
        
        $refParams = $refConstructor->getParameters();
        $this->assertTrue($refParams[0]->hasAnnotation('Named'));
        $this->assertEquals('webapp.xml.skin.default', $refParams[0]->getAnnotation('Named')->getName());
        
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('skin', $refParams[1]->getAnnotation('Named')->getName());
    }

    /**
     * skin checked should be passed thru
     *
     * @test
     */
    public function hasSkin()
    {
        $this->mockSkinGenerator->expects($this->once())
                                ->method('hasSkin')
                                ->with($this->equalTo('foo'))
                                ->will($this->returnValue(true));
        $this->assertTrue($this->cachingSkinGenerator->hasSkin('foo'));
    }

    /**
     * cached skin will not be regenerated
     *
     * @test
     */
    public function cached()
    {
        $this->mockSkinGenerator->expects($this->never())
                                ->method('generate');
        $this->mockCacheContainer->expects($this->once())
                                 ->method('has')
                                 ->with($this->equalTo(md5('foobaren_EN')))
                                 ->will($this->returnValue(true));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('get')
                                 ->with($this->equalTo(md5('foobaren_EN')))
                                 ->will($this->returnValue('<?xml version="1.0" encoding="utf-8"?><foo>bar</foo>'));
        $this->mockCacheContainer->expects($this->never())
                                 ->method('put');
        $result = $this->cachingSkinGenerator->generate('foo', 'bar', 'en_EN', '/');
        $this->assertInstanceOf('DOMDocument', $result);
        $this->assertEquals('<?xml version="1.0" encoding="utf-8"?>' . "\n<foo>bar</foo>\n",
                            $result->saveXML()
        );
    }

    /**
     * non-cached skin has to be created
     *
     * @test
     */
    public function nonCached()
    {
        $result = new DOMDocument();
        $result->loadXML('<?xml version="1.0" encoding="utf-8"?><foo>bar</foo>');
        $this->mockSkinGenerator->expects($this->once())
                                ->method('generate')
                                ->with($this->equalTo('foo'),
                                       $this->equalTo('bar'),
                                       $this->equalTo('en_EN'),
                                       $this->equalTo('/')
                                  )
                                ->will($this->returnValue($result));
        $this->mockCacheContainer->expects($this->once())
                                 ->method('has')
                                 ->with($this->equalTo(md5('foobaren_EN')))
                                 ->will($this->returnValue(false));
        $this->mockCacheContainer->expects($this->never())
                                 ->method('get');
        $this->mockCacheContainer->expects($this->once())
                                 ->method('put')
                                 ->with($this->equalTo(md5('foobaren_EN')),
                                        $this->equalTo('<?xml version="1.0" encoding="utf-8"?>' . "\n<foo>bar</foo>\n")
                                   );
        $this->assertSame($result,
                          $this->cachingSkinGenerator->generate('foo', 'bar', 'en_EN', '/')
        );
    }
}
?>