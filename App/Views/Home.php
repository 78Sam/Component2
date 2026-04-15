<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;

class Home extends AbstractTemplate
{
    public function run()
    {
        $components = $this->loadFile('app.html');
		
		$paragraph = $components['paragraph'];
		$paragraph
			->fill('text', 'Hello is a paragraph')
		;

		$main = $components['main'];
		$main
			->fill('titleVar', 'Sams cool document!')
			->fill('bodyVar', '<h1>Its a body!</h1>')
			->fill('bodyVar2', $paragraph)
		;

        // dump($components);

		echo $main;
    }
}
