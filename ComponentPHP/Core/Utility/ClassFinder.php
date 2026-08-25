<?php

declare(strict_types=1);

namespace Core\Utility;

class ClassFinder
{
    public function __construct(
        public bool $recursive = true,
    ) {
    }

    /**
     * @return list<\ReflectionClass>
     */
    public function byExtension(string $path, string $parentClassString): array
    {
        $results = [];
        $iterator = $this->getIterator($path);
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            $classString = fileToClassString($file);
            $reflectionClass = new \ReflectionClass($classString);

            $parentClass = $reflectionClass->getParentClass();
            if ($parentClass === false)
            {
                continue;
            }

            if ($parentClass->name === $parentClassString)
            {
                $results[] = $reflectionClass;
            }
        }

        return $results;
    }

    private function getIterator(string $path)
    {
        $fullPath = relativeToAbsolutePath($path);

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
