<?php

namespace Flying\ObjectBuilder\Tests\Fixtures\Handler\TargetProvider;

use Flying\ObjectBuilder\Exception\SkipDataException;
use Flying\ObjectBuilder\Handler\PrioritizedHandlerInterface;
use Flying\ObjectBuilder\Handler\TargetProvider\TargetProviderInterface;
use Flying\ObjectBuilder\Tests\Fixtures\TestObject\SkipDataObject;

class SkipDataProvider implements TargetProviderInterface, PrioritizedHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function canGetTarget(\ReflectionClass $reflection): bool
    {
        return $reflection->name === SkipDataObject::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget(\ReflectionClass $reflection, string $name): ?\Reflector
    {
        if ($name === 'skipped') {
            SkipDataException::throw();
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 100;
    }
}
