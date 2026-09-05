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
     * @template T
     *
     * @param class-string<T> $parentClassString
     *
     * @return list<\ReflectionClass<T>>
     */
    public function byExtension(string $path, string $parentClassString): array
    {
        $results = [];
        $iterator = $this->getIterator($path);
        /** @var \SplFileInfo $file */
        foreach ($iterator as $file)
        {
            $classString = fileToClassString($file);
            if ($classString === null)
            {
                continue;
            }

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

    private function getIterator(string $path): \RecursiveCallbackFilterIterator|\RecursiveIteratorIterator
    {
        $fullPath = relativeToAbsolutePath($path);

        $directoryIterator = new \RecursiveDirectoryIterator(
            $fullPath,
            \RecursiveDirectoryIterator::SKIP_DOTS,
        );

        $filterIterator = new \RecursiveCallbackFilterIterator(
            $directoryIterator,
            function (\SplFileInfo $file, string $_key, \RecursiveDirectoryIterator $iterator) {
                if ($iterator->hasChildren() && $this->recursive)
                {
                    return true;
                }

                return $file->getExtension() === 'php';
            },
        );

        if (!$this->recursive)
        {
            return $filterIterator;
        }

        return new \RecursiveIteratorIterator($filterIterator);
    }
}
