<?php

declare(strict_types=1);

namespace App\Views;

use ComponentPHP\Components\AbstractTemplate;

class Test extends AbstractTemplate
{
	
	#[\Override]
	protected function loadFiles(): void
	{
		$this->loadFile('test.html');
	}
}
