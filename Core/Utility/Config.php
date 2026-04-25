<?php

declare(strict_types=1);

namespace ComponentPHP\Utility;

class Config
{
    public const string ROOT_DIR = CPHP_ROOT_DIR;
    public const string LOG_DIR = self::ROOT_DIR . '/Log';
    public const string COMPONENTS_DIR = self::ROOT_DIR . '/App/Components';

    /** @var array<string, string> NAMESPACE_ALIASES */
    public const array NAMESPACE_ALIASES = [
        'Core' => 'ComponentPHP',
    ];

    public const bool IS_DEV = true;
    public const bool IS_PROD = !self::IS_DEV;

    public const TIMEZONE = 'BST';
}
