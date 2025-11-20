<?php

namespace Finite\Extractor;

interface StatePropertyExtractor
{
    public function extract(object $object, ?string $stateClass = null): \ReflectionProperty;


    /**
     * @return array<int, \ReflectionProperty>
     */
    public function extractAll(object $object): array;
}
