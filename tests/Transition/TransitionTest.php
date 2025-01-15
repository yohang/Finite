<?php

declare(strict_types=1);

namespace Finite\Tests\Transition;

use Finite\Exception\FiniteException;
use Finite\Exception\PropertyNotFoundException;
use Finite\Tests\Fixtures\SimpleArticleState;
use Finite\Transition\Transition;
use PHPUnit\Framework\TestCase;

class TransitionTest extends TestCase
{
    private Transition $object;

    protected function setUp(): void
    {
        $this->object = new Transition(
            'name',
            [SimpleArticleState::DRAFT],
            SimpleArticleState::PUBLISHED,
            ['property' => 'value', 'property2' => 'value2'],
        );
    }

    public function testItHasProperties(): void
    {
        $this->assertTrue($this->object->hasProperty('property'));
        $this->assertTrue($this->object->hasProperty('property2'));
        $this->assertFalse($this->object->hasProperty('property3'));
    }

    public function testItReturnsPropertyValue(): void
    {
        $this->assertSame('value', $this->object->getPropertyValue('property'));
        $this->assertSame('value2', $this->object->getPropertyValue('property2'));
    }

    public function testItThrowsExceptionWhenPropertyDoesNotExist(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectException(FiniteException::class);
        $this->expectException(PropertyNotFoundException::class);

        $this->expectExceptionMessage('Property "property3" does not exist');

        $this->object->getPropertyValue('property3');
    }

    public function testItReturnsPropertyNames(): void
    {
        $this->assertSame(['property', 'property2'], $this->object->getPropertyNames());
    }
}
