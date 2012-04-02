<?php
/**
 * A class with a __static method to be used in the test of
 * net.stubbles.stubClassLoader.
 *
 * @package     stubbles
 * @subpackage  test
 * @version     $Id: WithStatic.php 2857 2011-01-10 13:43:39Z mikey $
 */
/**
 * A class with a __static method to be used in the test of
 * net.stubbles.stubClassLoader.
 *
 * @package     stubbles
 * @subpackage  test
 */
class WithStatic
{
    private static $called = 0;
    
    public static function __static()
    {
        self::$called++;
    }
    
    public static function getCalled()
    {
        return self::$called;
    }
}
?>