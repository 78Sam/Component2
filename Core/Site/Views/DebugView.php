<?php

declare(strict_types=1);

namespace ComponentPHP\Site\Views;

use ComponentPHP\Components\AbstractTemplate;
use ComponentPHP\Components\Models\Component;
use ComponentPHP\Routing\Models\Request;
use ComponentPHP\Routing\Models\SiteMapEntry;

class DebugView extends AbstractTemplate
{
    protected function loadFiles(): void
    {
        $this->loadFile(__DIR__ . '/../Components/debug.html', absolutePath: true);
    }

    /**
     * @param Request[] $requests
     */
    public function mainPage(array $requests): Component
    {
        return $this
            ->get('main_page')
            ->fill('content', $this->requests($requests))
        ;
    }

    /**
     * @param Request[] $requests
     */
    public function requests(array $requests): string
    {
        $requestComponents = [];
        foreach ($requests as $request) {
            $port = $request->port === null ? '' : ":{$request->port}";
            $url = "{$request->scheme}://{$request->host}{$port}{$request->route}";
            $datetime = \DateTime::createFromTimestamp($request->time)->format('H:i:s d-m-Y');

            $requestComponents[] = $this
                ->get('request')
                ->fill('url', $url)
                ->fill('datetime', $datetime)
            ;
        }

        return '<h2>Requests</h2><br>' . implode('', $requestComponents);
    }

    /**
     * @param SiteMapEntry[] $siteMapEntries
     */
    public function siteMap(array $siteMapEntries): string
    {
        $siteMapComponents = [];
        foreach ($siteMapEntries as $siteMapEntry) {
            $siteMapComponents[] = $this
                ->get('site_map_entry')
                ->fill('route', $siteMapEntry->route)
                ->fill('name', $siteMapEntry->name)
                ->fill('class', $siteMapEntry->class)
                ->fill('method', $siteMapEntry->method)
            ;
        }

        return '<h2>Site Map</h2><br>' . implode('', $siteMapComponents);
    }

    public function phpinfo(): string
    {
        ob_start();
        phpinfo();

        return ob_get_clean();
    }
}
