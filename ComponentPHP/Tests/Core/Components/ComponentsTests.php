<?php

declare(strict_types=1);

namespace Tests\Core\Components;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Tests\Core\Components\Include\TestTemplate;

class ComponentsTests extends AbstractTest
{
    private TestTemplate $testTemplate;

    #[\Override]
    public function setup(): void
    {
        $this->testTemplate = new TestTemplate();
    }

    #[\Override]
    public function teardown(): void
    {}

    #[Test('Load a simple component')]
    public function loadSimpleComponent()
    {
        $componentNames = $this->testTemplate->loadFile('Tests/Core/Components/Include/Components/Simple.html');
        static::assertEquals($componentNames, ['simple'], 'Should have loaded ["simple"]');
    }

    #[Test('Render a simple component')]
    public function renderSimpleComponent()
    {
        $this->testTemplate->loadFile('Tests/Core/Components/Include/Components/Simple.html');
        $renderedValue = $this->testTemplate->get('simple')?->render();
        $expectedRender = '<p>simple</p>';
        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }
}
