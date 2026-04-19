<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

class Component implements \Stringable
{
    private const string VARIABLE_PATTERN = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

    /** @var array<string, string|self> $sockets */
    private array $sockets = [];

    public function __construct(
        public readonly string $name,
        public string $body,
        ?array $sockets = null,
    ) {
        $this->sockets = $sockets ?? $this->findSockets();
    }

    public static function __set_state(array $properties): self
    {
        return new self(...$properties);
    }

    public function __tostring(): string
    {
        return implode('', $this->sockets);
    }

    public function render(): string
    {
        return $this->__tostring();
    }

    public function fill(string $name, string|self $value): self
    {
        if (!\array_key_exists($name, $this->sockets)) {
            cphp_log(
                "Attempted to fill an undefined socket '{$name}' in component '{$this->name}'",
                level: 'warning',
                channel: \LoggingChannels::Templating,
            );

            return $this;
        }

        if (str_starts_with($name, '_chunk_')) {
            cphp_log(
                "Potentially attempting to fill generated socket '{$name}'",
                level: 'warning',
                channel: \LoggingChannels::Templating,
            );
        }

        $this->sockets[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    private function findSockets(): array
    {
        cphp_log("Computing sockets for component '{$this->name}'", channel: \LoggingChannels::Templating);

        $matches = [];
        preg_match_all(self::VARIABLE_PATTERN, $this->body, $matches);

        $result = [];
        $content = $this->body;
        for ($i = 0; $i < \count($matches[0]); $i++) {
            $split = explode($matches[0][$i], $content);
            $result["_chunk_{$i}"] = $split[0];
            $result[$matches['name'][$i]] = '';
            if (\count($split) > 1) {
                $content = $split[1];
            }
        }
        $result['_chunk_-1'] = $content;

        return $result;
    }
}
