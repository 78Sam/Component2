<?php

declare(strict_types=1);

namespace ComponentPHP\Utility;

define('COMPONENT_ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__, levels: 2)));

define('NAMESPACE_ALIASES', [
	'Core' => 'ComponentPHP',
	// 'App' => 'App',
]);

define('IS_DEV', true);