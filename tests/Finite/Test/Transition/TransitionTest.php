<?php

namespace Finite\Test\Transition;

use Finite\Transition\Transition;
use PHPUnit_Framework_TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class TransitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Transition
     */
    protected $object;

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolver
     */
    protected $optionsResolver;

    protected function setUp()
    {
        $this->optionsResolver = $this->getMockBuilder(OptionsResolver::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->object = new Transition('transition', ['s1'], 's2', null, $this->optionsResolver);
    }

    /**
     * @throws \Finite\Exception\TransitionException
     */
    public function testItResolvesOptions()
    {
        $this->optionsResolver
            ->expects($this->once())
            ->method('resolve')
            ->with(...[$this->isType('array')])
            ->willReturn(['foo' => 'bar'])
        ;

        $this->assertSame(['foo' => 'bar'], $this->object->resolveProperties(['baz' => 'qux']));
    }

    public function testItReturnsDefaultOptions()
    {
        $resolver = new OptionsResolver;
        $resolver->setDefaults(['p1' => 'foo', 'p2' => 'bar']);
        $transition = new Transition('transition', ['s1'], 's2', null, $resolver);

        $this->assertSame(['p1' => 'foo', 'p2' => 'bar'], $transition->getProperties());
        $this->assertTrue($transition->has('p1'));
        $this->assertFalse($transition->has('p3'));
        $this->assertSame('bar', $transition->get('p2'));
    }

    public function testItReturnsDefaultOptionsWhenSomeRequired()
    {
        $resolver = new OptionsResolver;
        $resolver->setDefaults(['p1' => 'foo', 'p2' => 'bar']);
        $resolver->setRequired(['p3']);
        $transition = new Transition('transition', ['s1'], 's2', null, $resolver);

        $this->assertSame(['p1' => 'foo', 'p2' => 'bar'], $transition->getProperties());
        $this->assertTrue($transition->has('p1'));
        $this->assertFalse($transition->has('p3'));
        $this->assertSame('bar', $transition->get('p2'));
    }
}
