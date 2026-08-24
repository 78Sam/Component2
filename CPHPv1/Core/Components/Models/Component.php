<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

use ComponentPHP\Cache\Cacheable;
use ComponentPHP\Components\Exceptions\UndefinedSocketException;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannel;
use ComponentPHP\Logging\Models\LoggingLevel;

class Component implements \Stringable, Cacheable
{
    /**
     * @param array<string, Socket> $sockets
     * @param array<string, list<string>> $socketPseudonyms
     */
    public function __construct(
        public readonly string $name,
        private array $sockets,
        private array $socketPseudonyms,
    ) {}

    #[\Override]
    public static function in(array $properties): self
    {
        return new self(...$properties);
    }

    #[\Override]
    public function out(): array
    {
        return [
            'name' => $this->name,
            'sockets' => $this->sockets,
            'socketPseudonyms' => $this->socketPseudonyms,
        ];
    }

    public function __toString(): string
    {
        return implode('', $this->sockets);
    }

    public function __clone()
    {
        $clonedSockets = [];
        foreach ($this->sockets as $key => $socket)
        {
            $clonedSockets[$key] = clone $socket;
        }
        $this->sockets = $clonedSockets;
    }

    public function render(): string
    {
        return $this->__toString();
    }

    /**
     * @throws UndefinedSocketException
     */
    public function fill(string $name, string|Component $value, bool $raw = false): self
    {
        if (!\array_key_exists($name, $this->socketPseudonyms)) {
            Logger::singleLog(
                "Attempted to fill an undefined socket '{$name}' in component '{$this->name}'",
                level: LoggingLevel::Error,
                channel: LoggingChannel::Templating,
            );

            throw new UndefinedSocketException(
                "Attempted to fill an undefined socket '{$name}' in component '{$this->name}'",
            );
        }

        if (\str_starts_with($name, '_chunk_')) {
            Logger::singleLog(
                "Potentially attempting to fill generated socket '{$name}'",
                level: LoggingLevel::Warning,
                channel: LoggingChannel::Templating,
            );
        }

        foreach ($this->socketPseudonyms[$name] as $pseudonym) {
            $this->sockets[$pseudonym]->value = $raw || $value instanceof Component ? $value : htmlspecialchars($value);
        }

        return $this;
    }
}
