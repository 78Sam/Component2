<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    public function home(): Component
    {
        return $this->get('app')
            ->fill('body', $this->get('splash'));
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('base.html');
        $this->loadFile('Home/home.html');
    }
}
