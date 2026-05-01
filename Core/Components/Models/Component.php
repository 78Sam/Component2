<?php

declare(strict_types=1);

namespace ComponentPHP\Components\Models;

use ComponentPHP\Components\Exceptions\UndefinedSocketException;
use ComponentPHP\Logging\Logger;
use ComponentPHP\Logging\Models\LoggingChannels;
use ComponentPHP\Logging\Models\LoggingLevel;

class Component implements \Stringable
{
    private const string VARIABLE_PATTERN = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

    /**
     * @param array<string, Socket> $sockets
     * @param array<string, list<string>> $socketPseudonyms
     */
    public function __construct(
        public readonly string $name,
        private array $sockets,
        private array $socketPseudonyms,
    ) {}

    /**
     * @param array{name: string, sockets: array<string, Socket>, socketPseudonyms: array<string, list<string>>} $properties
     */
    public static function __set_state(array $properties): self
    {
        return new self(...$properties);
    }

    public function __toString(): string
    {
        return implode('', $this->sockets);
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
                channel: LoggingChannels::Templating,
            );

            throw new UndefinedSocketException(
                "Attempted to fill an undefined socket '{$name}' in component '{$this->name}'",
            );
        }

        if (\str_starts_with($name, '_chunk_')) {
            Logger::singleLog(
                "Potentially attempting to fill generated socket '{$name}'",
                level: LoggingLevel::Warning,
                channel: LoggingChannels::Templating,
            );
        }

        foreach ($this->socketPseudonyms[$name] as $pseudonym) {
            $this->sockets[$pseudonym]->value = $raw || $value instanceof Component ? $value : htmlspecialchars($value);
        }

        return $this;
    }
}
