<?php

declare(strict_types=1);

namespace ComponentPHP\Utility;

define('CPHP_ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__, levels: 2)));
define('CPHP_LOG_DIR', CPHP_ROOT_DIR . '/Log');
define('CPHP_COMPONENTS_DIR', CPHP_ROOT_DIR . '/App/Components');

define('CPHP_NAMESPACE_ALIASES', [
    'Core' => 'ComponentPHP',
    // 'App' => 'App',
]);

define('CPHP_IS_DEV', true);

define('CPHP_TIMEZONE', 'BST');
