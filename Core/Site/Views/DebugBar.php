<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;

class DebugBar extends AbstractTemplate
{
    protected function loadFiles(): void
    {
        $this->loadFile(__DIR__ . '/../Components/debugBar.html', absolutePath: true);
    }

    /**
     * @param array{timeTaken: float, memoryUsage: int} $debugMetrics
     */
    public function bar(array $debugMetrics): Component
    {
        return $this
            ->get('debug_bar')
            ->fill('timeTaken', (string) $debugMetrics['timeTaken'])
            ->fill('memoryUsage', (string) $debugMetrics['memoryUsage'])
        ;
    }
}
