<?php

declare(strict_types=1);

namespace Finite\Tests\Extractor;

use Finite\Exception\NonUniqueStateException;
use Finite\Extractor\ReflectionStatePropertyExtractor;
use Finite\Tests\Fixtures\AlternativeArticle;
use Finite\Tests\Fixtures\AlternativeArticleState;
use Finite\Tests\Fixtures\Article;
use Finite\Tests\Fixtures\SimpleArticleState;
use PHPUnit\Framework\TestCase;

final class ReflectionStatePropertyExtractorTest extends TestCase
{
    private ReflectionStatePropertyExtractor $object;

    protected function setUp(): void
    {
        $this->object = new ReflectionStatePropertyExtractor();
    }

    public function testItExtractSimpleState(): void
    {
        $property = $this->object->extract(new Article('test'));

        $this->assertSame('state', $property->getName());
        $this->assertSame(SimpleArticleState::class, $property->getType()->getName());
    }

    public function testItThrowsOnAlternativeStateWithoutDetails(): void
    {
        $this->expectException(NonUniqueStateException::class);
        $property = $this->object->extract(new AlternativeArticle('test'));
    }

    public function testItExtractAlternativeState(): void
    {
        $property = $this->object->extract(new AlternativeArticle('test'), AlternativeArticleState::class);

        $this->assertSame('alternativeState', $property->getName());
        $this->assertSame(AlternativeArticleState::class, $property->getType()->getName());

        $property = $this->object->extract(new AlternativeArticle('test'), SimpleArticleState::class);

        $this->assertSame('state', $property->getName());
        $this->assertSame(SimpleArticleState::class, $property->getType()->getName());
    }
}
