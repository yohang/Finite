<?php

declare(strict_types=1);

namespace Finite\Extractor;

use Finite\State;

final class ReflectionStatePropertyExtractor implements StatePropertyExtractor
{
    use StatePropertyExtractorTrait;

    #[\Override]
    public function extractAll(object $object): array
    {
        $properties = [];

        $reflectionClass = new \ReflectionClass($object);
        /** @psalm-suppress DocblockTypeContradiction */
        do {
            foreach ($reflectionClass->getProperties() as $property) {
                /** @var \ReflectionUnionType|\ReflectionIntersectionType|\ReflectionNamedType $reflectionType */
                $reflectionType = $property->getType();
                if (null === $reflectionType) {
                    continue;
                }

                if ($reflectionType instanceof \ReflectionUnionType) {
                    continue;
                }

                if ($reflectionType instanceof \ReflectionIntersectionType) {
                    continue;
                }

                /** @var class-string $name */
                $name = $reflectionType->getName();
                if (!enum_exists($name)) {
                    continue;
                }

                $reflectionEnum = new \ReflectionEnum($name);
                /** @psalm-suppress TypeDoesNotContainType */
                if ($reflectionEnum->implementsInterface(State::class)) {
                    $properties[] = $property;
                }
            }
        } while ($reflectionClass = $reflectionClass->getParentClass());

        return $properties;
    }
}
