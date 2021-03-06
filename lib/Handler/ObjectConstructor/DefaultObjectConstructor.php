<?php

namespace Flying\ObjectBuilder\Handler\ObjectConstructor;

class DefaultObjectConstructor implements ObjectConstructorInterface
{
    /**
     * {@inheritdoc}
     */
    public function canConstruct(\ReflectionClass $reflection, array $data): bool
    {
        return $reflection->isInstantiable();
    }

    /**
     * {@inheritdoc}
     */
    public function construct(\ReflectionClass $reflection, array &$data)
    {
        return $reflection->newInstance();
    }
}
