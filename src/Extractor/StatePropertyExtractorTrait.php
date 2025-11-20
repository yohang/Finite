<?php

declare(strict_types=1);

namespace Finite\Extractor;

use Finite\Exception\BadStateClassException;
use Finite\Exception\NonUniqueStateException;
use Finite\Exception\NoStateFoundException;

trait StatePropertyExtractorTrait
{
    /**
     * @param class-string|null $stateClass
     */
    #[\Override]
    public function extract(object $object, ?string $stateClass = null): \ReflectionProperty
    {
        if ($stateClass && !enum_exists($stateClass)) {
            throw new NoStateFoundException(\sprintf('Enum "%s" does not exists', $stateClass));
        }

        $properties = $this->extractAll($object);
        if (null !== $stateClass) {
            foreach ($properties as $property) {
                if ((string) $property->getType() === $stateClass) {
                    return $property;
                }
            }

            throw new BadStateClassException(\sprintf('Found no state on object "%s" with class "%s"', $object::class, $stateClass));
        }

        if (0 === \count($properties)) {
            throw new NoStateFoundException('Found no state on object '.$object::class);
        }

        if (1 === \count($properties)) {
            return $properties[0];
        }

        throw new NonUniqueStateException('Found multiple states on object '.$object::class);
    }

    /**
     * @return array<int, \ReflectionProperty>
     */
    abstract public function extractAll(object $object): array;
}
