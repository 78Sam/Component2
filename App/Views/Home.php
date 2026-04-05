<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Model\Component;

class Home extends AbstractTemplate
{
    protected static function draw(array $values): Component
    {
        $heading = self::loadComponent('heading.html')
            ->fill('value', 'Yoooo!')
        ;
        
        $app = self::loadComponent('home.html')
            ->fill('app', $heading)
            ->fill('head', '<style>some dumb ah head value</style>')
        ;

        return $app;
    }
}
