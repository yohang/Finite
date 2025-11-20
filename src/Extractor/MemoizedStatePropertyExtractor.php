<?php

namespace Finite\Extractor;

final class MemoizedStatePropertyExtractor implements StatePropertyExtractor
{
    use StatePropertyExtractorTrait;

    /**
     * @var array <object, \ReflectionProperty[]>
     */
    private array $cache = [];

    public function __construct(
        private readonly StatePropertyExtractor $decorated = new ReflectionStatePropertyExtractor(),
    )
    {
    }

    #[\Override]
    public function extractAll(object $object): array
    {
        if (isset($this->cache[get_class($object)])) {
            return $this->cache[get_class($object)];
        }

        return $this->cache[get_class($object)] = $this->decorated->extractAll($object);
    }
}
