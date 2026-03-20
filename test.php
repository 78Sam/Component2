<?php

function test1()
{
	print_r(debug_backtrace(limit: 1));
}

function test2()
{
	test1();
}

function test3()
{
	test2();
}

test3();