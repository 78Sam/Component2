<?php

declare(strict_types=1);

use Core\Testing\AbstractTest;
use Core\Testing\TestRunner;
use Core\Utility\ClassFinder;

/** @var \Composer\Autoload\ClassLoader $classLoader */
$classLoader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$psr4Namespaces = [];
foreach ($classLoader->getPrefixesPsr4() as $namespace => $paths)
{
    foreach ($paths as $path)
    {
        $psr4Namespaces[normalisePath(realpath($path))] = trim($namespace, '\\');
    }
}
define('PSR4_NAMESPACES', $psr4Namespaces);

// $directoryIterator = new ClassFinder(recursive:true);
// print_r($directoryIterator->byExtension('Tests/Core/Resolvers', AbstractTest::class));

$tester = new TestRunner();
$tester->runAllTests();
