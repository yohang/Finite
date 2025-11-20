<?php

declare(strict_types=1);

namespace Finite\Extractor;

interface StatePropertyExtractor
{
    /**
     * @param class-string|null $stateClass
     */
    public function extract(object $object, ?string $stateClass = null): \ReflectionProperty;

    /**
     * @return array<int, \ReflectionProperty>
     */
    public function extractAll(object $object): array;
}
