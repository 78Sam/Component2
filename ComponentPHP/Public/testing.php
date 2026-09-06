<?php

declare(strict_types=1);

use Core\Routing\Router;
use Core\Testing\AbstractTest;
use Core\Testing\TestRunner;
use Core\Utility\ClassFinder;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\StringValidator;

/** @var \Composer\Autoload\ClassLoader $classLoader */
$classLoader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$psr4Namespaces = [];
foreach ($classLoader->getPrefixesPsr4() as $namespace => $paths) {
    foreach ($paths as $path) {
        $psr4Namespaces[normalisePath(realpath($path))] = trim($namespace, '\\');
    }
}
/** @var array<string, string> */
define('PSR4_NAMESPACES', $psr4Namespaces);

// $tester = new TestRunner();
// $tester->runAllTests();

$router = new Router();
$router->createSiteMap();
print_r($router->siteMapEntries);
