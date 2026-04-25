<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    public function home(): Component
    {
        return $this->get('h1')->fill('h1', 'Home');
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('home.html');
    }
}
