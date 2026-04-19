<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    public function draw(array $arguments = []): string|Component
    {
        $components = $this->loadFile('app.html');

        $paragraph = $components['paragraph'];
        $paragraph->fill('text', 'Hello is a paragraph');

        $main = $components['main'];

        return $main
            ->fill('titleVar', 'Sams cool document!')
            ->fill('bodyVar', '<h1>Its a body!</h1>')
            ->fill('bodyVar2', $paragraph)
        ;
    }
}
