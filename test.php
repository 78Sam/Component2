<?php

$content = file_get_contents('test.html');
// echo $content;

// $componentPattern = '/<component\s+!@\(\s*#component\|(?<name>\w+)\s*\)>(?<component>.*?)<\/component>/s';
$componentPattern = '/!@\(\s*\$(?<name>[a-zA-Z_]+\w*)\s*\)/';

$matches = [];
preg_match_all($componentPattern, $content, $matches);
print_r($matches);

// $components = [];
// $content = preg_replace_callback($componentPattern, function ($value) use (&$components) {
//     // print_r($value);
//     $components[] = ['name' => $value['name'], 'comp' => trim($value['component'])];

//     return '';
// }, $content);
// print_r($components);
// print_r(trim($content));