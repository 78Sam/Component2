<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    protected function loadFiles(): void
    {
        $this->loadFile('app.html');
    }

    public function home(): string|Component
    {
        return $this
            ->get('main')
            ->fill('titleVar', 'Sams cool document!')
            ->fill('bodyVar', '<h1>Its a body!</h1>')
            ->fill('bodyVar2', $this->get('paragraph')->fill('text', 'Hello this is a paragraph'))
        ;
    }
}
