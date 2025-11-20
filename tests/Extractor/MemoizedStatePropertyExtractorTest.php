<?php

namespace Finite\Tests\Extractor;

use Finite\Extractor\MemoizedStatePropertyExtractor;
use Finite\Extractor\StatePropertyExtractor;
use PHPUnit\Framework\TestCase;

final class MemoizedStatePropertyExtractorTest extends TestCase
{
    public function testItMemoizeResult(): void
    {
        $decorated = $this->createMock(StatePropertyExtractor::class);
        $extractor = new MemoizedStatePropertyExtractor($decorated);

        $decorated
            ->expects($this->once())
            ->method('extractAll')
            ->with($this->isInstanceOf(\stdClass::class))
            ->willReturn([$this->createMock(\ReflectionProperty::class)]);

        $object = new \stdClass();
        $extractor->extractAll($object);
        $extractor->extractAll($object);
    }
}
