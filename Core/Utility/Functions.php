<?php

declare(strict_types=1);

function dump(mixed $value): void
{
    /** @var array $backtrace */
    $backtrace = debug_backtrace(limit: 1)[0];

    echo '<pre>';
    echo "{$backtrace['file']}::{$backtrace['line']}\n\n";
    print_r($value);
    echo "\n\n</pre>";
}
