<?php

declare(strict_types=1);

namespace Finite\Tests\Event;

use Finite\Event\CanTransitionEvent;
use Finite\Tests\Fixtures\SimpleArticleState;
use Finite\Transition\TransitionInterface;
use PHPUnit\Framework\TestCase;

class CanTransitionEventTest extends TestCase
{
    public function testItStopsPropagation(): void
    {
        $event = new CanTransitionEvent(
            $this->createMock(\stdClass::class),
            $this->createMock(TransitionInterface::class),
            SimpleArticleState::DRAFT,
        );

        $this->assertFalse($event->isPropagationStopped());
        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }

    public function testItBlocksTransition(): void
    {
        $event = new CanTransitionEvent(
            $this->createMock(\stdClass::class),
            $this->createMock(TransitionInterface::class),
            SimpleArticleState::DRAFT,
        );

        $event->blockTransition();
        $this->assertTrue($event->isPropagationStopped());
        $this->assertTrue($event->isTransitionBlocked());
    }
}
