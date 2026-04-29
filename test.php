<?php

$p = [
	'a' => 'hi there',
	'b' => 'yo',
];

print_r(http_build_query($p, encoding_type: PHP_QUERY_RFC3986));
