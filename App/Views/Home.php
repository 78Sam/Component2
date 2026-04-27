<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class Home extends AbstractTemplate
{
    public function home(): Component
    {
        $numberContainer = $this->getNumberContainer(1);

        return $this
            ->get('app')
            ->fill('title', 'Random numbers')
            ->fill('body', $numberContainer)
        ;
    }

    public function getNumberContainer(): Component
    {
        $randomNumber = $this->getRandomNumber();

        return $this->get('number_container')
            ->fill('randomNumbers', $randomNumber);
    }

    public function getRandomNumber(): Component
    {
        return $this->get('random_number')
            ->fill('randomNumber', (string) rand(1, 100));
    }

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile('home.html');
    }
}
