<?php

declare(strict_types=1);

namespace Finite\Tests\Dumper;

use Finite\Dumper\MermaidDumper;
use Finite\Tests\Fixtures\SimpleArticleState;
use PHPUnit\Framework\TestCase;

class MermaidDumperTest extends TestCase
{
    public function testItDumps(): void
    {
        $this->assertSame(
            <<<MERMAID
            ---
            title: Finite\Tests\Fixtures\SimpleArticleState
            ---
            stateDiagram-v2
                draft --> published: publish
                reported --> published: clear
                disabled --> published: clear
                published --> reported: report
                reported --> disabled: disable
                published --> disabled: disable
            MERMAID,
            (new MermaidDumper())->dump(SimpleArticleState::class)
        );
    }
}
