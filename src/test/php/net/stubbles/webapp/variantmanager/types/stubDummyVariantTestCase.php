<?php
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubDummyVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubDummyVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubDummyVariant');
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubDummyVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubDummyVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that a dummy variant is never valid and never enforcing
     *
     * @test
     */
    public function valid()
    {
        $dummyVariant = new stubDummyVariant();
        $session      = $this->getMock('stubSession');
        $request      = $this->getMock('stubRequest');
        $this->assertFalse($dummyVariant->isEnforcing($session, $request));
        $this->assertFalse($dummyVariant->isValid($session, $request));
    }
}
?>