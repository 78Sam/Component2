<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Model;

class Component
{
    private const string PLACEHOLDER_PATTERN = '/\{\{[ ]*(?P<placeholder>[a-zA-Z]+[a-zA-Z0-9]*)[ ]*\}\}/';

    /**
     * @param array<string, Component|string> $subComponents
     */
    public function __construct(
        public readonly string $path,
        public readonly string $content,
        private array $subComponents = [],
    ) {
    }

    public function fill(string $block, Component|string $component): self
    {
        $this->subComponents[$block] = $component;

        return $this;
    }

    public function render(): string
    {
        return preg_replace_callback(self::PLACEHOLDER_PATTERN, function($matches) {
            $placeholderName = $matches['placeholder'];
            if (!\array_key_exists($placeholderName, $this->subComponents))
            {
                return 'UNDEFINED PLACEHOLDER VALUE';
            }

            $value = $this->subComponents[$placeholderName];
            if (\is_string($value))
            {
                return $value;
            }

            return $value->render();
        }, $this->content);
    }
}
