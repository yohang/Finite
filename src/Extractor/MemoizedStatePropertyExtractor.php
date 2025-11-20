<?php

declare(strict_types=1);

namespace Finite\Extractor;

final class MemoizedStatePropertyExtractor implements StatePropertyExtractor
{
    use StatePropertyExtractorTrait;

    /**
     * @var array <class-string, array<int, \ReflectionProperty>>
     */
    private array $cache = [];

    public function __construct(
        private readonly StatePropertyExtractor $decorated = new ReflectionStatePropertyExtractor(),
    ) {
    }

    #[\Override]
    public function extractAll(object $object): array
    {
        if (isset($this->cache[$object::class])) {
            /** @var array<int, \ReflectionProperty> $value */
            $value = $this->cache[$object::class];

            return $value;
        }

        return $this->cache[$object::class] = $this->decorated->extractAll($object);
    }
}
