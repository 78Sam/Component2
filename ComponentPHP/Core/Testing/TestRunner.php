<?php

declare(strict_types=1);

namespace Core\Testing;

use Core\Testing\Attributes\Test;
use Core\Utility\ClassFinder;

final class TestRunner
{
    private ClassFinder $classFinder;
    /** @var list<\ReflectionClass> */
    private array $testClasses = [];

    public function __construct()
    {
        $this->classFinder = new ClassFinder();
        $this->testClasses = $this->classFinder->byExtension('Tests', AbstractTest::class);
    }

    public function runAllTests()
    {
        foreach ($this->testClasses as $testClass)
        {
            $classString = $testClass->name;
            print_r("\n\nRunning tests for '{$classString}':\n");
            $classInstance = new ($classString)();
            foreach ($testClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
            {
                if (count($method->getAttributes(Test::class)) === 0)
                {
                    continue;
                }
                $this->runTest($classInstance, $method);
            }
        }
    }

    private function runTest(object $classInstance, \ReflectionMethod $method)
    {
        $methodName = $method->name;
        print_r("- {$methodName}\n");
        try
        {
            $classInstance->$methodName();
        }
        catch (\Throwable $th)
        {}
    }
}
