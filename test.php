<?php

$content = file_get_contents('test.html');
// echo $content;

$componentPattern = '/<component\s+!@\(\s*#component\|(?P<name>\w+)\s*\)>(?P<component>.*\n*)*?<\/component>/';

// $matches = [];
// preg_match_all($componentPattern, $content, $matches);
// print_r($matches);

$components = [];
$content = preg_replace_callback($componentPattern, function ($value) use (&$components) {
    // print_r($value);
    $components[] = ['name' => $value['name'], 'comp' => $value['component']];

    return '';
}, $content);
print_r($components);
print_r(trim($content));