<?php

declare(strict_types=1);

namespace ComponentPHP\Utility;

if (!defined('CPHP_ROOT_DIR')) {
    /** @var string */
    define('CPHP_ROOT_DIR', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__DIR__, levels: 2)));
}
