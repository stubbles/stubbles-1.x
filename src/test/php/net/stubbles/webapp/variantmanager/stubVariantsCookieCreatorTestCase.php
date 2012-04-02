<?php
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsCookieCreator.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubVariantsCookieCreatorTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::stubVariantsCookieCreator');
/**
 * Test for net::stubbles::webapp::variantmanager::stubVariantsCookieCreator.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubVariantsCookieCreatorTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @var  stubVariantsCookieCreator
     */
    protected $variantsCookieCreator;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->variantsCookieCreator = new stubVariantsCookieCreator();
    }

    /**
     * @test
     */
    public function classIsAnnotatedAsSingleton()
    {
        $this->assertTrue($this->variantsCookieCreator->getClass()
                                                      ->hasAnnotation('Singleton')
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetCookieMapNameMethod()
    {
        $setCookieMapNameMethod = $this->variantsCookieCreator->getClass()->getMethod('setCookieMapName');
        $this->assertTrue($setCookieMapNameMethod->hasAnnotation('Inject'));
        $this->assertTrue($setCookieMapNameMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setCookieMapNameMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.variantmanager.cookie.mapname',
                            $setCookieMapNameMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetCookieNameMethod()
    {
        $setCookieNameMethod = $this->variantsCookieCreator->getClass()->getMethod('setCookieName');
        $this->assertTrue($setCookieNameMethod->hasAnnotation('Inject'));
        $this->assertTrue($setCookieNameMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setCookieNameMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.variantmanager.cookie.name',
                            $setCookieNameMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetCookieLifetimeMethod()
    {
        $setCookieLifetimeMethod = $this->variantsCookieCreator->getClass()->getMethod('setCookieLifetime');
        $this->assertTrue($setCookieLifetimeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setCookieLifetimeMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setCookieLifetimeMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.variantmanager.cookie.lifetime',
                            $setCookieLifetimeMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetCookieDomainMethod()
    {
        $setCookieDomainMethod = $this->variantsCookieCreator->getClass()->getMethod('setCookieDomain');
        $this->assertTrue($setCookieDomainMethod->hasAnnotation('Inject'));
        $this->assertTrue($setCookieDomainMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setCookieDomainMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.variantmanager.cookie.url',
                            $setCookieDomainMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationsPresentOnSetCookiePathMethod()
    {
        $setCookiePathMethod = $this->variantsCookieCreator->getClass()->getMethod('setCookiePath');
        $this->assertTrue($setCookiePathMethod->hasAnnotation('Inject'));
        $this->assertTrue($setCookiePathMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setCookiePathMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.variantmanager.cookie.path',
                            $setCookiePathMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function usesDefaultValuesForVariantCookie()
    {
        $variantCookie = $this->variantsCookieCreator->createVariantCookie('foo');
        $this->assertEquals('variant', $variantCookie->getName());
        $this->assertEquals('foo', $variantCookie->getValue());
        $this->assertGreaterThanOrEqual(time() + 7776000, $variantCookie->getExpiration());
        $this->assertEquals('/', $variantCookie->getPath());
        $this->assertNull($variantCookie->getDomain());
    }

    /**
     * @test
     */
    public function usesDifferentValuesForVariantCookie()
    {
        $variantCookie = $this->variantsCookieCreator->setCookieName('otherName')
                                                     ->setCookieLifetime(10)
                                                     ->setCookieDomain('example.com')
                                                     ->setCookiePath('/bar/')
                                                     ->createVariantCookie('foo');
        $this->assertEquals('otherName', $variantCookie->getName());
        $this->assertEquals('foo', $variantCookie->getValue());
        $this->assertGreaterThanOrEqual(time() + 10, $variantCookie->getExpiration());
        $this->assertEquals('/bar/', $variantCookie->getPath());
        $this->assertEquals('example.com', $variantCookie->getDomain());
    }

    /**
     * @test
     */
    public function usesDefaultValuesForMapCookie()
    {
        $mapCookie = $this->variantsCookieCreator->createMapCookie('2010-12-13');
        $this->assertEquals('variant_configname', $mapCookie->getName());
        $this->assertEquals('2010-12-13', $mapCookie->getValue());
        $this->assertGreaterThanOrEqual(time() + 7776000, $mapCookie->getExpiration());
        $this->assertEquals('/', $mapCookie->getPath());
        $this->assertNull($mapCookie->getDomain());
    }

    /**
     * @test
     */
    public function usesDifferentValuesForMapCookie()
    {
        $mapCookie = $this->variantsCookieCreator->setCookieMapName('otherName')
                                                 ->setCookieLifetime(10)
                                                 ->setCookieDomain('example.com')
                                                 ->setCookiePath('/bar/')
                                                 ->createMapCookie('foo');
        $this->assertEquals('otherName', $mapCookie->getName());
        $this->assertEquals('foo', $mapCookie->getValue());
        $this->assertGreaterThanOrEqual(time() + 10, $mapCookie->getExpiration());
        $this->assertEquals('/bar/', $mapCookie->getPath());
        $this->assertEquals('example.com', $mapCookie->getDomain());
    }
}
?>