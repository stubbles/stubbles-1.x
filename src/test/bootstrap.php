<?php
define('TEST_SRC_PATH',dirname(__FILE__));
require_once dirname(__FILE__) . '/../../bootstrap.php';
require_once 'star/starReader.php';
StarClassRegistry::addLibPath(stubBootstrap::getRootPath() . '/lib');
stubBootstrap::init(stubBootstrap::getRootPath() . '/projects/dist',
                    '/../src/main/php/net/stubbles/stubClassLoader.php');
?>