<?php

declare(strict_types=1);

namespace Finite\Tests\Event;

use Finite\Event\Event;
use Finite\Event\EventDispatcher;
use PHPUnit\Framework\TestCase;

class EventDispatcherTest extends TestCase
{
    public function testItDispatchObject(): void
    {
        $objectMock = $this->getMockBuilder(\stdClass::class)->disableOriginalConstructor()->getMock();

        $listenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $listenerMock->expects($this->once())->method('__invoke')->with($objectMock);

        $badListenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $badListenerMock->expects($this->never())->method('__invoke')->with($objectMock);

        $dispatcher = new EventDispatcher();
        $dispatcher->addEventListener($objectMock::class, $listenerMock);
        $dispatcher->addEventListener(Event::class, $badListenerMock);

        $dispatcher->dispatch($objectMock);
    }

    public function testItDispatchEvent(): void
    {
        $objectMock = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();

        $listenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $listenerMock->expects($this->once())->method('__invoke')->with($objectMock);

        $badListenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $badListenerMock->expects($this->never())->method('__invoke')->with($objectMock);

        $dispatcher = new EventDispatcher();
        $dispatcher->addEventListener($objectMock::class, $listenerMock);
        $dispatcher->addEventListener(Event::class, $badListenerMock);

        $dispatcher->dispatch($objectMock);
    }

    public function testItDispatchMultipleEvent(): void
    {
        $objectMock = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();

        $listenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $listenerMock->expects($this->once())->method('__invoke')->with($objectMock);

        $otherListenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $otherListenerMock->expects($this->once())->method('__invoke')->with($objectMock);

        $dispatcher = new EventDispatcher();
        $dispatcher->addEventListener($objectMock::class, $listenerMock);
        $dispatcher->addEventListener($objectMock::class, $otherListenerMock);

        $dispatcher->dispatch($objectMock);
    }

    public function testItStopsPropagation(): void
    {
        $objectMock = $this->getMockBuilder(Event::class)->disableOriginalConstructor()->getMock();
        $objectMock->expects($this->once())->method('stopPropagation');
        $objectMock->expects($this->once())->method('isPropagationStopped')->willReturn(true);

        $listenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $listenerMock
            ->expects($this->once())
            ->method('__invoke')
            ->with($objectMock)
            ->willReturnCallback(fn (Event $e) => $e->stopPropagation());

        $otherListenerMock = $this->getMockBuilder(\stdClass::class)->addMethods(['__invoke'])->getMock();
        $otherListenerMock->expects($this->never())->method('__invoke')->with($objectMock);

        $dispatcher = new EventDispatcher();
        $dispatcher->addEventListener($objectMock::class, $listenerMock);
        $dispatcher->addEventListener($objectMock::class, $otherListenerMock);

        $dispatcher->dispatch($objectMock);
    }
}
