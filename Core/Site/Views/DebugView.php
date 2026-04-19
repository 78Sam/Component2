<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;
use ComponentPHP\Routing\Models\Request;

class DebugView extends AbstractTemplate
{
    /** @var array<string, Component> $components */
    private array $components = [];

    private bool $componentsLoaded = false;

    /**
     * @param Request[] $requests
     */
    public function mainPage(array $requests): Component
    {
        $this->loadComponents();

        return $this->components['mainPage']->fill('content', $this->requests($requests));
    }

    /**
     * @param Request[] $requests
     */
    public function requests(array $requests): string
    {
        $this->loadComponents();
        $requestComponent = $this->components['request'];

        $requestComponents = [];
        foreach ($requests as $request)
        {
            $nextRequest = clone $requestComponent;
            $requestComponents[] = $nextRequest
                ->fill('route', $request->route)
                ->fill('scheme', $request->scheme)
            ;
        }

        return '<h2>Requests</h2>' . implode('', $requestComponents);
    }

    private function loadComponents(): void
    {
        if ($this->componentsLoaded)
        {
            return;
        }

        $this->componentsLoaded = true;
        $this->components = $this->loadFile(__DIR__ . '/../Components/debug.html', absolutePath: true);
    }
}
