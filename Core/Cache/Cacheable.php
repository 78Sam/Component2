<?php

declare(strict_types=1);

namespace ComponentPHP\Cache;

interface Cacheable
{
	public function _export(): array;

	public function _import(array $properties): self;
}
