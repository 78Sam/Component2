<?php

declare(strict_types=1);

// $variablePattern = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

// $html = file_get_contents('test.html');

// $chunks = [];

// /** @var array<string, array{count: int, pseudonyms: list<string>}> $variableNames */
// $variableNames = [];
// $splitOrdering = [];
// $html = preg_replace_callback($variablePattern, function ($match) use (&$variableNames, &$splitOrdering) {
// 	$name = $match['name'];
// 	if (!array_key_exists($name, $variableNames))
// 	{
// 		$variableNames[$name] = ['count' => 0, 'pseudonyms' => []];
// 	}
// 	$count = $variableNames[$name]['count'];
// 	$replacement = "_chunk_variable_{$name}_{$count}";

// 	$variableNames[$name]['count']++;
// 	$variableNames[$name]['pseudonyms'][] = $replacement;
// 	$splitOrdering[] = $replacement;

// 	return $replacement;
// }, $html);

// foreach ($variableNames as $name => &$value)
// {
// 	$value['count'] = 0;
// }

// print_r($html);
// print_r($variableNames);

// $chunks = [];
// $count = 0;
// $body = $html;
// foreach ($splitOrdering as $variableName)
// {
// 	$split = explode($variableName, $body);
// 	$chunks["_chunk_{$count}"] = $split[0];
// 	$count++;
// 	$chunks[$variableName] = '';
// 	$body = $split[1] ?? '';
// }
// $chunks['_chunk_-1'] = $body;

// print_r($chunks);

$x = [
	'a' => ['b'=> 0, 'c' => ['hi', 'hello']],
	'b' => ['b'=> 2, 'c' => ['yo', 'sup']],
];

print_r(array_map(fn ($item) => $item = $item['c'], $x));