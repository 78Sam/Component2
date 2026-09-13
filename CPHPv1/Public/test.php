<?php

declare(strict_types=1);

use ComponentPHP\Components\AbstractTemplate;

require_once 'vendor/autoload.php';

class Test2 extends AbstractTemplate
{
    #[Override]
    protected function loadFiles(): void
    {
        $this->loadFile('test.html');
    }
}

$x = new Test2();
$component = $x->get('test_component');
$component->fill('myVar', 'hi there');
$component->fill('newVar', 'hi there 2');
echo $component->__toString();