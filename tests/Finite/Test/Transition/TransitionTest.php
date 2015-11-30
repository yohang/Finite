<?php

namespace Finite\Test\Transition;

use Finite\Transition\Transition;

/**
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class TransitionTest extends \PHPUnit_Framework_TestCase
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
        $this->optionsResolver = $this->getMockBuilder('Symfony\Component\OptionsResolver\OptionsResolver')
            ->disableOriginalConstructor()
            ->getMock();
        $this->object = new Transition('transition', ['s1'], 's2', null, $this->optionsResolver);
    }

    public function testItResolvesOptions()
    {
        $this->optionsResolver
            ->expects($this->once())
            ->method('resolve')
            ->with($this->isType('array'))
            ->will($this->returnValue(['foo' => 'bar']));

        $this->assertSame(['foo' => 'bar'], $this->object->resolveProperties(['baz' => 'qux']));
    }
}
