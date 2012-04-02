<?php
/**
 * Stream wrapper to enable inclusion of external xsl stylesheets into Stubbles' master.xsl.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_util
 * @version     $Id: stubXslImportStreamWrapper.php 3220 2011-11-14 15:33:46Z mikey $
 */
/**
 * Stream wrapper to enable inclusion of external xsl stylesheets into Stubbles' master.xsl.
 *
 * @package     stubbles
 * @subpackage  xml_xsl_util
 */
class stubXslImportStreamWrapper extends stubBaseObject
{
    /**
     * base xsl to return
     *
     * @var  string
     */
    protected static $xsl;
    /**
     * list of default imports
     *
     * @var  array<string>
     */
    protected static $defaultImports = array('stubbles.php?xsl/copy.xsl',
                                             'stubbles.php?xsl/stub.xsl',
                                             'stubbles.php?xsl/ingrid.xsl',
                                             'stubbles.php?xsl/variant.xsl'
                                       );
    /**
     * current reading offset
     *
     * @var  int
     */
    protected $offset     = 0;

    /**
     * initializes the stream wrapper
     *
     * @param  string  $configPath   path to xsl-imports.ini file
     * @param  string  $cachePath    path to store cached xsl
     * @param  bool    $enableCache  whether to cache or not
     * @todo   think about using stubProperties instead of doing raw ini file work
     */
    public static function init($configPath, $cachePath, $enableCache)
    {
        static $registered;
        if (true !== $registered) {
            stream_wrapper_register('xslimport', __CLASS__);
            $registered = true;
        }
        
        if (true === $enableCache && file_exists($cachePath . DIRECTORY_SEPARATOR . 'xsl-imports.cache') === true) {
            self::$xsl = file_get_contents($cachePath . '/xsl-imports.cache');
            return;
        }
        
        self::$xsl = '<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">' . "\n";
        $source = 'star://' . urlencode(stubBootstrap::getRootPath() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
        foreach (self::$defaultImports as $import) {
            self::$xsl .= '  <xsl:import href="' . $source . $import . "\"/>\n";
        }
        
        if (file_exists($configPath . DIRECTORY_SEPARATOR . 'xsl-imports.ini') === true) {
            $imports = @parse_ini_file($configPath . DIRECTORY_SEPARATOR . 'xsl-imports.ini', true);
            if (false !== $imports) {
                foreach ($imports as $source => $sourceImports) {
                    if ('lib' === $source) {
                        $source = 'star://' . urlencode(stubBootstrap::getRootPath() . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
                    } elseif ('resources' === $source) {
                        $source = 'file://' . urlencode(stubBootstrap::getSourcePath() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);
                    }
        
                    foreach ($sourceImports as $import) {
                        if (in_array($import, self::$defaultImports) === false) {
                            self::$xsl .= '  <xsl:import href="' . $source . $import . "\"/>\n";
                        }
                    }
                }
            }
        }
        
        self::$xsl .= '</xsl:stylesheet>';
        if (true === $enableCache) {
            file_put_contents($cachePath . DIRECTORY_SEPARATOR . 'xsl-imports.cache', self::$xsl);
        }
    }

    /**
     * open the stream
     *
     * @param   string  $path         the path to open
     * @param   string  $mode         mode for opening
     * @param   string  $options      options for opening
     * @param   string  $opened_path  full path that was actually opened
     * @return  bool
     */
    public function stream_open($path, $mode, $options, $opened_path)
    {
        return (null !== self::$xsl);
    }

    /**
     * closes the stream
     */
    public function stream_close()
    {
        // nothing to do here
    }

    /**
     * read the stream up to $count bytes
     *
     * @param   int     $count  amount of bytes to read
     * @return  string
     */
    public function stream_read($count)
    {
        $substring     = substr(self::$xsl, $this->offset, $count);
        $this->offset += $count;
        return $substring;
    }

    /**
     * checks whether stream is at end of file
     *
     * @return  bool
     */
    public function stream_eof()
    {
        return ($this->offset >= strlen(self::$xsl));
    }

    /**
     * returns status of stream
     *
     * @return  array
     */
    public function stream_stat()
    {
        return array('size' => strlen(self::$xsl));
    }

    /**
     * returns status of url
     *
     * @param   string  $path  path of url to return status for
     * @return  array
     */
    public function url_stat($path)
    {
        return array('size' => strlen(self::$xsl));
    }
}
?>