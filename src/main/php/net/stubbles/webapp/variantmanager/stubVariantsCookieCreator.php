<?php
/**
 * Factory to create variant related cookies.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @version     $Id: stubVariantsCookieCreator.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::ipo::response::stubCookie');
/**
 * Factory to create variant related cookies.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager
 * @Singleton
 */
class stubVariantsCookieCreator extends stubBaseObject
{
    /**
     * name of cookie where current map name is stored
     *
     * @var  string
     */
    protected $cookieMapName   = 'variant_configname';
    /**
     * name of cookie to be used to store the variant
     *
     * @var  string
     */
    protected $cookieName      = 'variant';
    /**
     * lifetime for the variant cookie, defaults to 90 days
     *
     * @var  int
     */
    protected $cookieLifetime  = 7776000; // 90 days
    /**
     * url of the variant cookie
     *
     * @var  string
     */
    protected $cookieDomain    = null;
    /**
     * path of the variant cookie
     *
     * @var  string
     */
    protected $cookiePath      = '/';

    /**
     * sets the name of cookie where current map name is stored
     *
     * @param   string                     $cookieMapName
     * @return  stubVariantsCookieCreator
     * @Inject(optional=true)
     * @Named('net.stubbles.variantmanager.cookie.mapname')
     */
    public function setCookieMapName($cookieMapName)
    {
        $this->cookieMapName = $cookieMapName;
        return $this;
    }

    /**
     * returns the name of cookie where current map name is stored
     *
     * @return  string
     */
    public function getCookieMapName()
    {
        return $this->cookieMapName;
    }

    /**
     * sets the name of cookie to be used to store the variant
     *
     * @param   string                     $cookieName
     * @return  stubVariantsCookieCreator
     * @Inject(optional=true)
     * @Named('net.stubbles.variantmanager.cookie.name')
     */
    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;
        return $this;
    }

    /**
     * returns the name of cookie to be used to store the variant
     *
     * @return  string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     * sets the lifetime for the variant cookie
     *
     * @param   int                        $cookieLifetime
     * @return  stubVariantsCookieCreator
     * @Inject(optional=true)
     * @Named('net.stubbles.variantmanager.cookie.lifetime')
     */
    public function setCookieLifetime($cookieLifetime)
    {
        $this->cookieLifetime = $cookieLifetime;
        return $this;
    }

    /**
     * sets the domain of the variant cookie
     *
     * @param   string                     $cookieDomain
     * @return  stubVariantsCookieCreator
     * @Inject(optional=true)
     * @Named('net.stubbles.variantmanager.cookie.url')
     */
    public function setCookieDomain($cookieDomain)
    {
        $this->cookieDomain = $cookieDomain;
        return $this;
    }

    /**
     * sets the path of the variant cookie
     *
     * @param   string                     $cookiePath
     * @return  stubVariantsCookieCreator
     * @Inject(optional=true)
     * @Named('net.stubbles.variantmanager.cookie.path')
     */
    public function setCookiePath($cookiePath)
    {
        $this->cookiePath = $cookiePath;
        return $this;
    }

    /**
     * creates the cookie which stores the variant
     *
     * @param   string      $variantName
     * @return  stubCookie
     */
    public function createVariantCookie($variantName)
    {
        return stubCookie::create($this->cookieName, $variantName)
                         ->expiringAt(time() + $this->cookieLifetime)
                         ->forPath($this->cookiePath)
                         ->forDomain($this->cookieDomain);
    }

    /**
     * creates the cookie which stores the name of the map
     *
     * @param   string      $variantMapName
     * @return  stubCookie
     */
    public function createMapCookie($variantMapName)
    {
        return stubCookie::create($this->cookieMapName, $variantMapName)
                         ->expiringAt(time() + $this->cookieLifetime)
                         ->forPath($this->cookiePath)
                         ->forDomain($this->cookieDomain);
    }
}
?>