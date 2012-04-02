<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubXmlVariantFactory.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubXmlVariantFactoryTestCase.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubXmlVariantFactory',
                      'net::stubbles::webapp::variantmanager::types::stubAbstractVariant'
);
@include_once 'vfsStream/vfsStream.php';
/**
 * Helper class for the test.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 */
class SelfDefinedVariant extends stubAbstractVariant
{
    /**
     * parameter from config
     *
     * @var  string
     */
    protected $param;

    /**
     * sets parameter
     *
     * @param  string  $value
     */
    public function setParam($value)
    {
        $this->param = $value;
    }

    /**
     * returns parameter
     *
     * @return  string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * sets another param
     *
     * @param  string  $other
     */
    public function setOther($other)
    {
        $this->other = $other;
    }

    /**
     * check whether the variant is an enforcing variant
     *
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  boolean
     */
    public function isEnforcing(stubSession $session, stubRequest $request)
    {
        // intentionally empty
    }

    /**
     * check whether the variant is valid
     *
     * @param   stubSession  $session  access to session
     * @param   stubRequest  $request  access to request parameters
     * @return  boolean
     */
    public function isValid(stubSession $session, stubRequest $request)
    {
        // intentionally empty
    }
}
/**
 * Test for net::stubbles::webapp::variantmanager::stubXmlVariantFactory.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @since       1.6.0
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubXmlVariantFactoryTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubXmlVariantFactory
     */
    protected $xmlVariantFactory;
    /**
     * mocked resource loader
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockResourceLoader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        if (class_exists('vfsStream', false) === false) {
            $this->markTestSkipped(__CLASS__ . ' requires vfsStream, see http://vfs.bovigo.org/.');
        }

        vfsStream::setup();
        $this->mockResourceLoader = $this->getMock('stubResourceLoader');
        $this->xmlVariantFactory  = new stubXmlVariantFactory($this->mockResourceLoader,
                                                              vfsStream::url('root')
                                    );
    }

    /**
     * @test
     */
    public function isDefaultImplementationForVariantFactory()
    {
        $refClass = new stubReflectionClass('stubVariantFactory');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals('net::stubbles::webapp::variantmanager::stubXmlVariantFactory',
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getFullQualifiedClassName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $constructor = $this->xmlVariantFactory->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));

        $parameters = $constructor->getParameters();
        $this->assertTrue($parameters[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.config.path', $parameters[1]->getAnnotation('Named')->getName());
    }

    /**
     * @test
     * @expectedException  stubFileNotFoundException
     */
    public function missingConfigFileThrowsFileNotFoundException()
    {
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function invalidConfigurationFileThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array()));
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * helper method
     */
    protected function createVariantConfig()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <requestParam name="request" title="Request based variant" paramName="var"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array()));
    }

    /**
     * @test
     */
    public function validConfigurationFileParsedIntoVariantsMap()
    {
        $this->createVariantConfig();
        $variantsMap = $this->xmlVariantFactory->getVariantsMap();
        $this->assertInstanceOf('stubVariantsMap', $variantsMap);
    }

    /**
     * @test
     */
    public function variantsMapContainsName()
    {
        $this->createVariantConfig();
        $this->assertEquals('default',
                            $this->xmlVariantFactory->getVariantsMap()->getName()
        );
    }

    /**
     * @test
     */
    public function persistenceIsEnabledByDefault()
    {
        $this->createVariantConfig();
        $this->assertTrue($this->xmlVariantFactory->getVariantsMap()->shouldUsePersistence());
    }

    /**
     * @test
     */
    public function persistenceCanBeDisabledViaAttribute()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default" usePersistence="false">
  <requestParam name="request" title="Request based variant" paramName="var"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array()));
        $this->assertFalse($this->xmlVariantFactory->getVariantsMap()->shouldUsePersistence());
    }

    /**
     * @test
     */
    public function variantsMapContainsThreeMainVariants()
    {
        $this->createVariantConfig();
        $children = $this->xmlVariantFactory->getVariantsMap()->getRootVariant()->getChildren();
        $this->assertEquals(3, count($children));
        $this->assertArrayHasKey('request', $children);
        $this->assertInstanceOf('stubRequestParamVariant', $children['request']);
        $this->assertArrayHasKey('random1', $children);
        $this->assertInstanceOf('stubRandomVariant', $children['random1']);
        $this->assertArrayHasKey('random2', $children);
        $this->assertInstanceOf('stubRandomVariant', $children['random2']);
    }

    /**
     * @test
     */
    public function random1VariantContainsChildVariant()
    {
        $this->createVariantConfig();
        $children = $this->xmlVariantFactory->getVariantsMap()->getRootVariant()->getChildren();
        $this->assertArrayHasKey('random1', $children);
        $random1Children = $children['random1']->getChildren();
        $this->assertEquals(1, count($random1Children));
        $this->assertArrayHasKey('lead', $random1Children);
        $this->assertInstanceOf('stubLeadVariant', $random1Children['lead']);
    }

    /**
     * @test
     */
    public function allVariantsContainRequiredValues()
    {
        $this->createVariantConfig();
        $children = $this->xmlVariantFactory->getVariantsMap()->getRootVariant()->getChildren();
        $this->assertArrayHasKey('request', $children);
        $this->assertEquals('request', $children['request']->getName());
        $this->assertEquals('Request based variant', $children['request']->getTitle());
        $this->assertEquals('', $children['request']->getAlias());

        $this->assertArrayHasKey('random1', $children);
        $this->assertEquals('random1', $children['random1']->getName());
        $this->assertEquals('Random variant 1', $children['random1']->getTitle());
        $this->assertEquals('', $children['random1']->getAlias());
        $this->assertEquals(1, $children['random1']->getWeight());

        $random1Children = $children['random1']->getChildren();
        $this->assertArrayHasKey('lead', $random1Children);
        $this->assertEquals('lead', $random1Children['lead']->getName());
        $this->assertEquals('Lead variant', $random1Children['lead']->getTitle());
        $this->assertEquals('foo', $random1Children['lead']->getAlias());

        $this->assertArrayHasKey('random2', $children);
        $this->assertEquals('random2', $children['random2']->getName());
        $this->assertEquals('Random variant 2', $children['random2']->getTitle());
        $this->assertEquals('', $children['random2']->getAlias());
        $this->assertEquals(2, $children['random2']->getWeight());
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function missingRequiredAttributeThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <requestParam name="request" title="Request based variant" paramName="var"/>
  <random name="random1" title="Random variant 1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array()));
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function selfDefinedVariantTagWithoutDefinitionThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <defined name="selfdefined" title="Variant tag without definition"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array()));
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function selfDefinedVariantTagWithMissingClassDefinitionThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <defined name="selfdefined" title="Variant tag with missing class definition"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('variantmanager/variantmanager.ini')
                 ->withContent('[defined]')
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array(vfsStream::url('root/variantmanager/variantmanager.ini'))));
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * @test
     */
    public function selfDefinedVariantTag()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <defined name="selfdefined" title="Variant tag with definition" param="baz"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('variantmanager/variantmanager.ini')
                 ->withContent("[defined]
class=SelfDefinedVariant
param=required
other=optional
")
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array(vfsStream::url('root/variantmanager/variantmanager.ini'))));
        $children = $this->xmlVariantFactory->getVariantsMap()->getRootVariant()->getChildren();
        $this->assertArrayHasKey('selfdefined', $children);
        $this->assertEquals('selfdefined', $children['selfdefined']->getName());
        $this->assertEquals('Variant tag with definition', $children['selfdefined']->getTitle());
        $this->assertEquals('', $children['selfdefined']->getAlias());
        $this->assertEquals('baz', $children['selfdefined']->getParam());
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function selfDefinedVariantTagWithMissingRequiredAttributeThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <defined name="selfdefined" title="Variant tag with definition"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('variantmanager/variantmanager.ini')
                 ->withContent("[defined]
class=SelfDefinedVariant
param=required
other=optional
")
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array(vfsStream::url('root/variantmanager/variantmanager.ini'))));
        $this->xmlVariantFactory->getVariantsMap();
    }

    /**
     * @test
     * @expectedException  stubVariantConfigurationException
     */
    public function selfDefinedVariantTagWithInvalidAttributeThrowsVariantConfigurationException()
    {
        vfsStream::newFile('variantmanager.xml')
                 ->withContent('<?xml version="1.0" encoding="utf-8"?>
<variants name="default">
  <defined name="selfdefined" title="Variant tag with definition"/>
  <random name="random1" title="Random variant 1" weight="1">
    <lead name="lead" title="Lead variant" alias="foo"/>
  </random>
  <random name="random2" title="Random variant 2" weight="2"/>
</variants>')
                 ->at(vfsStreamWrapper::getRoot());
        vfsStream::newFile('variantmanager/variantmanager.ini')
                 ->withContent("[defined]
class=SelfDefinedVariant
invalid<attribute=required
")
                 ->at(vfsStreamWrapper::getRoot());
        $this->mockResourceLoader->expects($this->any())
                                 ->method('getResourceUris')
                                 ->will($this->returnValue(array(vfsStream::url('root/variantmanager/variantmanager.ini'))));
        $this->xmlVariantFactory->getVariantsMap();
    }
}
?>