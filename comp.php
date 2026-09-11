<?php

declare(strict_types=1);

$options = ['franken', 'server'];

$help = static function() use ($options) {
    $optionsString = implode('|', $options);
    print_r("php comp.php <{$optionsString}>\n");

    exit;
};

if ($argc !== 2) {
    $help();
}

$input = strtolower($argv[1]);
if (!in_array($input, $options)) {
    $help();
}

if ($input === 'server') {
    exec('cd ComponentPHP/Public && php -S localhost:8080');

    exit;
}

$output = [];
$found = false;
exec('docker images --format json', $output);

if (count($output) !== 0) {
    foreach ($output as $image)
    {
        $image = json_decode($output[0], true);
        if ($image === null) {
            throw new \Exception("Unable to decode image json: '{$image}'");
        }

        if (($image['Repository'] ?? null) === 'component_php')
        {
            $found = true;

            break;
        }
    }   
}

if (!$found) {
    print_r("Please run 'docker build -t component_php .' and retry\n");

    exit;
}

exec('docker compose up');
