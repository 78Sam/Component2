<?php

$x = [];
$x['sam']['hi'] = 'hi';
$x['sam']['hello'] = 78;

print_r($x);

foreach ($x as $key => $value)
{
	if ($value['hello'] === 78)
	{
		echo 'yeah';
	}
}