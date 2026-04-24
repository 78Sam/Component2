<?php

$start = hrtime(true);

sleep(1);

echo ((hrtime(true) - $start) / 10**9) . PHP_EOL;