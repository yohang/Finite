<?php
declare(strict_types=1);

namespace Finite\Tests\Extension\Twig;

use Finite\Extension\Twig\FiniteExtension;
use Finite\StateMachine;
use PHPUnit\Framework\TestCase;

class FiniteExtensionTest extends TestCase
{
    public function test_it_declare_twig_functions(): void
    {
        $object = $this->createMock(\stdClass::class);
        $stateMachine = $this->createMock(StateMachine::class);

        $functions = (new FiniteExtension($stateMachine))->getFunctions();

        $functions = array_combine(
            array_map(fn($function) => $function->getName(), $functions),
            $functions,
        );
        $this->assertArrayHasKey('finite_can', $functions);
        $this->assertArrayHasKey('finite_reachable_transitions', $functions);

        $stateMachine->expects($this->once())->method('can')->with($object, 'publish')->willReturn(true);
        $functions['finite_can']->getCallable()($object, 'publish');

        $stateMachine->expects($this->once())->method('getReachablesTransitions')->with($object)->willReturn([]);
        $functions['finite_reachable_transitions']->getCallable()($object);
    }
}
