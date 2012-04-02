<?php
/**
 * Bootstrap file for web applications.
 *
 * @package  stubbles
 * @version  $Id: index.php 3243 2011-11-30 12:03:06Z mikey $
 */
// load Stubbles
require '../../../bootstrap.php';
stubBootstrap::run('org::stubbles::dist::DistWebApp', 'dist');
?>