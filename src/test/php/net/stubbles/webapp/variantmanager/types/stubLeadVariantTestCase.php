<?php
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubLeadVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @version     $Id: stubLeadVariantTestCase.php 3170 2011-08-23 15:00:43Z mikey $
 */
stubClassLoader::load('net::stubbles::webapp::variantmanager::types::stubLeadVariant');
/**
 * Test for net::stubbles::webapp::variantmanager::types::stubLeadVariant.
 *
 * @package     stubbles
 * @subpackage  webapp_variantmanager_test
 * @group       webapp
 * @group       webapp_variantmanager
 */
class stubLeadVariantTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * test that a lead variant is always valid but never enforcing
     *
     * @test
     */
    public function valid()
    {
        $leadVariant = new stubLeadVariant();
        $session     = $this->getMock('stubSession');
        $request     = $this->getMock('stubRequest');
        $this->assertFalse($leadVariant->isEnforcing($session, $request));
        $this->assertTrue($leadVariant->isValid($session, $request));
    }
}
?>