<?php
/**
 * Test for net::stubbles::webapp::xml::generator::stubVariantListGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @version     $Id: stubVariantListGeneratorTestCase.php 3255 2011-12-02 12:26:00Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubLeadVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRandomVariant',
                      'net::stubbles::webapp::variantmanager::types::stubRootVariant',
                      'net::stubbles::webapp::xml::generator::stubVariantListGenerator',
                      'net::stubbles::xml::stubXmlStreamWriterProvider'
);
/**
 * Test for net::stubbles::webapp::xml::generator::stubVariantListGenerator.
 *
 * @package     stubbles
 * @subpackage  webapp_xml_generator_test
 * @group       webapp
 * @group       webapp_xml
 * @group       webapp_xml_generator
 */
class stubVariantListGeneratorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubVariantListGenerator
     */
    protected $variantListGenerator;
    /**
     * mocked session instance
     *
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSession;
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
        $this->mockSession          = $this->getMock('stubSession');
        $this->mockInjector         = $this->getMock('stubInjector');
        $this->variantListGenerator = new stubVariantListGenerator($this->mockSession,
                                                                   $this->mockInjector
                                      );
    }

    /**
     * @test
     */
    public function annotationsPresentOnConstructor()
    {
        $this->assertTrue($this->variantListGenerator->getClass()
                                                     ->getConstructor()
                                                     ->hasAnnotation('Inject')
        );
    }

    /**
     * @test
     */
    public function doesNothingOnStartup()
    {
        $this->variantListGenerator->startup();
    }

    /**
     * @test
     */
    public function isAlwaysCachable()
    {
        $this->assertTrue($this->variantListGenerator->isCachable());
    }

    /**
     * @test
     */
    public function hasNoSpecialCacheVars()
    {
        $this->assertEquals(array(),
                            $this->variantListGenerator->getCacheVars()
        );
    }

    /**
     * @test
     */
    public function createsEmptyListIfNoVariantInSession()
    {
        $this->mockSession->expects($this->once())
                          ->method('hasValue')
                          ->will($this->returnValue(false));
        $this->mockInjector->expects($this->never())
                           ->method('getInstance');
        $provider        = new stubXmlStreamWriterProvider();
        $xmlStreamWriter = $provider->get();
        $this->variantListGenerator->generate($xmlStreamWriter, $this->getMock('stubXMLSerializer', array(), array(), '', false));
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<variants>' .
  '<variantList/>' .
'</variants>', $xmlStreamWriter->asXML());
    }

    /**
     * @test
     */
    public function createsVariantListIfVariantInSession()
    {
        $lead1 = new stubLeadVariant();
        $lead1->setName('lead');
        $lead1->setTitle('Main variant');
        $rootVariant = new stubRootVariant();
        $rootVariant->addChild($lead1);
        $lead2 = new stubLeadVariant();
        $lead2->setName('other');
        $lead2->setTitle('Other variant');
        $rootVariant->addChild($lead2);
        $randomVariant1 = new stubRandomVariant();
        $randomVariant1->setName('foo');
        $randomVariant1->setTitle('A foo');
        $randomVariant2 = new stubRandomVariant();
        $randomVariant2->setName('bar');
        $randomVariant2->setTitle('A bar');
        $lead1->addChild($randomVariant1);
        $lead1->addChild($randomVariant2);
        $mockVariantFactory = $this->getMock('stubVariantFactory');
        $variantMap = new stubVariantsMap($rootVariant);
        $mockVariantFactory->expects($this->once())
                           ->method('getVariantsMap')
                           ->will($this->returnValue($variantMap));
        $this->mockSession->expects($this->once())
                          ->method('hasValue')
                          ->will($this->returnValue(true));
        $this->mockInjector->expects($this->once())
                           ->method('getInstance')
                           ->will($this->returnValue($mockVariantFactory));
        $provider        = new stubXmlStreamWriterProvider();
        $xmlStreamWriter = $provider->get();
        $this->variantListGenerator->generate($xmlStreamWriter, $this->getMock('stubXMLSerializer', array(), array(), '', false));
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
'<variants>' .
  '<variantList>' .
    '<variant name="lead" title="Main variant" type="net::stubbles::webapp::variantmanager::types::stubLeadVariant">' .
      '<variant name="lead:foo" title="A foo" type="net::stubbles::webapp::variantmanager::types::stubRandomVariant"/>' .
      '<variant name="lead:bar" title="A bar" type="net::stubbles::webapp::variantmanager::types::stubRandomVariant"/>' .
    '</variant>' .
    '<variant name="other" title="Other variant" type="net::stubbles::webapp::variantmanager::types::stubLeadVariant"/>' .
  '</variantList>' .
'</variants>', $xmlStreamWriter->asXML());
    }

    /**
     * @test
     */
    public function doesNothingOnCleanup()
    {
        $this->variantListGenerator->cleanup();
    }
}
?>