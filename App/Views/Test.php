<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Test extends AbstractTemplate
{
    public function test(): Component
    {
        return $this->get('test')
            ->fill('stuff', 'Its duplicated!');
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('test.html');
    }
}
