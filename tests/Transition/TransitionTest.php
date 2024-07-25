<?php
declare(strict_types=1);

namespace Finite\Tests\Transition;

use Finite\State;
use Finite\Transition\Transition;
use PHPUnit\Framework\TestCase;

class TransitionTest extends TestCase
{
    private Transition $object;

    protected function setUp(): void
    {
        $targetState = $this->createMock(State::class);

        $this->object = new Transition(
            'name',
            ['source'],
            $targetState,
            ['property' => 'value', 'property2' => 'value2'],
        );
    }

    public function test_it_has_properties(): void
    {
        $this->assertTrue($this->object->hasProperty('property'));
        $this->assertTrue($this->object->hasProperty('property2'));
        $this->assertFalse($this->object->hasProperty('property3'));
    }

    public function test_it_returns_property_value(): void
    {
        $this->assertSame('value', $this->object->getPropertyValue('property'));
        $this->assertSame('value2', $this->object->getPropertyValue('property2'));
    }

    public function test_it_throws_exception_when_property_does_not_exist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Property "property3" does not exist');

        $this->object->getPropertyValue('property3');
    }

    public function test_it_returns_property_names(): void
    {
        $this->assertSame(['property', 'property2'], $this->object->getPropertyNames());
    }
}
