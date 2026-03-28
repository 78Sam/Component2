<?php

class Test
{
	public function __construct(
		public readonly string $x,
	) {
	}

	public static function __set_state($properties)
	{
		return new Test(...$properties);
	}
}

$p = new Test("sam");

$tester = '$x = ' . var_export($p, return: true) . ';';
echo $tester;
eval($tester);
var_dump($x);