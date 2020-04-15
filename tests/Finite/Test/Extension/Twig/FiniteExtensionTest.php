<?php

namespace Finite\Test\Extension\Twig;

use Finite\Context;
use Finite\Extension\Twig\FiniteExtension;
use Finite\Factory\PimpleFactory;
use Finite\State\Accessor\StateAccessorInterface;
use Finite\State\State;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit_Framework_TestCase;
use Pimple;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class FiniteExtensionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    protected $accessor;

    public function setUp()
    {
        $this->accessor = $accessor = $this->createMock(StateAccessorInterface::class);
        $this->env = new Twig_Environment(
            new Twig_Loader_Array(
                [
                    'state' => '{{ finite_state(object) }}',
                    'transitions' => '{% for transition in finite_transitions(object) %}{{ transition }}{% endfor %}',
                    'properties' => '{% for property, val in finite_properties(object) %}{{ property }}{% endfor %}',
                    'has' => '{{ finite_has(object, property) ? "yes" : "no" }}',
                    'can' => '{{ finite_can(object, transition, "foo") ? "yes" : "no" }}',
                ]
            )
        );

        $container = new Pimple(
            [
                'state_machine' => static function () use ($accessor) {
                    $sm = new StateMachine(null, null, $accessor);
                    $sm->addState(new State('s1', State::TYPE_INITIAL, [], ['foo' => true, 'bar' => false]));
                    $sm->addTransition('t12', 's1', 's2');
                    $sm->addTransition('t23', 's2', 's3');

                    return $sm;
                },
            ]
        );

        $this->context = new Context(new PimpleFactory($container, 'state_machine'));
        $this->env->addExtension(new FiniteExtension($this->context));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testState()
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertSame('s1', $this->env->render('state', ['object' => $this->getObjectMock()]));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testTransitions()
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertSame('t12', $this->env->render('transitions', ['object' => $this->getObjectMock()]));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testProperties()
    {
        $this->accessor->expects($this->once())->method('getState')->willReturn('s1');
        $this->assertSame('foobar', $this->env->render('properties', ['object' => $this->getObjectMock()]));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testHas()
    {
        $this->accessor->expects($this->exactly(2))->method('getState')->willReturn('s1');
        $this->assertSame('yes', $this->env->render('has', ['object' => $this->getObjectMock(), 'property' => 'foo']));
        $this->assertSame('no', $this->env->render('has', ['object' => $this->getObjectMock(), 'property' => 'baz']));
    }

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testCan()
    {
        $this->assertSame('yes', $this->env->render('can', ['object' => $this->getObjectMock(), 'transition' => 't12']));
        $this->assertSame('no', $this->env->render('can', ['object' => $this->getObjectMock(), 'transition' => 't23']));
    }

    public function getObjectMock()
    {
        return $this->createMock(StatefulInterface::class);
    }
}
