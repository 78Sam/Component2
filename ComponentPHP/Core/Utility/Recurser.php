<?php

declare(strict_types=1);

namespace Core\Utility;

use Core\Testing\AbstractTest;

class Recurser
{
    public function __construct(
        public bool $recursive = true,
    ) {
    }

    public function byExtension(string $path, string $class): array
    {
        $iterator = $this->getIterator($path);
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            // print_r($file->getFilename() . "\n");
            // TODO: This needs to become its own function in Functions or something (i.e. pathToClassName) or even pathToReflectionClass
            $classPath = substr($file->getRealPath(), 0, -(strlen($file->getExtension()) + 1));
            $namespace = str_replace(DIRECTORY_SEPARATOR, '\\', explode('ComponentPHP/', $classPath)[1]);
            print_r("NS IS '{$namespace}'\n");
            $class = new \ReflectionClass($namespace);
            if ($class->getParentClass() === false)
            {
                continue;
            }

            $parentClass = $class->getParentClass();
            $pc = $parentClass->name;
            print_r("{$pc}\n");
            if ($parentClass->name === AbstractTest::class)
            {
                print_r('YAYA');
                print_r($class->getParentClass());
            }
        }

        return [];
    }

    private function getIterator(string $path)
    {
        $fullPath = Config::ROOT_DIR . '/' . trim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/');
        print_r("path is '{$fullPath}'\n");

        $directoryIterator = new \RecursiveDirectoryIterator(
            $fullPath,
            \RecursiveDirectoryIterator::SKIP_DOTS,
        );

        $filterIterator = new \RecursiveCallbackFilterIterator(
            $directoryIterator,
            function (\SplFileInfo $someval, string $key, \RecursiveDirectoryIterator $iterator) {
                if ($iterator->hasChildren() && $this->recursive)
                {
                    return true;
                }

                return $someval->getExtension() === 'php';
            },
        );

        if (!$this->recursive)
        {
            return $filterIterator;
        }

        return new \RecursiveIteratorIterator($filterIterator);
    }
}
