<?php

declare(strict_types=1);

interface Thing
{
	public function export(): array;
}

class ABC implements Thing
{
	public function __construct(
		public string $x,
	) {
	}

	public function export(): array
	{
		return [
			'x' => $this->x,
		];
	}

	public static function create(array $properties): self
	{
		return new self(...$properties);
	}
}

class Test implements Thing
{
	public function __construct(
		public array $x,
		public array $y,
	) {}

	public function export(): array
	{
		return [
			'x' => $this->x,
			'y' => $this->y,
		];
	}

	public static function create(array $properties): self
	{
		return new self(...$properties);
	}
}

function exportIt(mixed $data): mixed
{
	if (is_array($data)) {
		$items = [];
		foreach ($data as $key => $value) {
			$value = exportIt($value);
			$key = exportIt($key);
			$items[] = "{$key} => {$value}";
		}

		return '[' . implode(', ', $items) . ']';
	}

	if (is_object($data)) {
		if ($data instanceof Thing) {
			$properties = exportIt($data->export());
			$class = $data::class;

			return "{$class}::create({$properties})";
		}

		return var_export($data, return: true);
	}

	if (is_string($data)) {
		return "'{$data}'";
	}

	return $data;
}

$p = new Test(x: ['hi', 'hello', 123], y: [new ABC('sam'), new ABC('uma'), new \DateTime('now')]);

$export = '$new = ' . exportIt($p) . ';';
print_r($export);
