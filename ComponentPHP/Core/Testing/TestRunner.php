<?php

declare(strict_types=1);

namespace Core\Testing;

use Core\Testing\Attributes\Test;
use Core\Utility\ClassFinder;
use Core\Utility\Console;

final class TestRunner
{
    private ClassFinder $classFinder;
    /** @var list<\ReflectionClass<AbstractTest>> */
    private array $testClasses = [];

    public function __construct()
    {
        $this->classFinder = new ClassFinder();
        $this->testClasses = $this->classFinder->byExtension('Tests', AbstractTest::class);
    }

    public function runAllTests(): void
    {
        foreach ($this->testClasses as $testClass)
        {
            $classString = $testClass->name;

            /** @var AbstractTest $class */
            $class = new $classString();
            $class->setup();

            $testResults = ['passed' => 0, 'total' => 0];
            print_r("Running tests for class {$classString}\n");
            foreach ($testClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method)
            {
                $testAttributes = $method->getAttributes(Test::class);
                if (count($testAttributes) === 0)
                {
                    continue;
                }

                if (count($testAttributes) > 1)
                {
                    throw new \LogicException('Each test method should only have one test attribute');
                }

                $testResults['total']++;
                $methodName = $method->name;
                $testAttribute = $testAttributes[0]->newInstance();
                $testMessage = "{$methodName} [{$testAttribute->description}]";

                $class->preTest($testAttribute);
                $error = $this->runTest($class, $methodName);
                $class->postTest($testAttribute);

                if ($error !== null)
                {
                    $testMessage .= " ({$error->getMessage()})";
                }
                else
                {
                    $testResults['passed']++;
                }

                $resultColour = $error === null ? Console::BG_COLOUR_GREEN : Console::BG_COLOUR_RED;
                print_r(' - ' . Console::message($testMessage, background: $resultColour));
            }
            $class->teardown();
            print_r("{$testResults['passed']}/{$testResults['total']} passed\n\n");
        }
    }

    private function runTest(object $class, string $method): ?\Throwable
    {
        try
        {
            $class->$method();

            return null;
        }
        catch (\Throwable $th)
        {
            return $th;
        }
    }
}
