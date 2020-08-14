<?php

namespace Finite\Test\Extension\Twig;

use Finite\Context;
use Finite\Extension\Twig\FiniteExtension;
use Finite\Factory\PimpleFactory;
use  Finite\StateMachine\StateMachine;
use Finite\State\State;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class FiniteExtensionTest extends \PHPUnit_Framework_TestCase
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
        if (!class_exists('Twig_Environment')) {
            class_alias('Twig\Environment', 'Twig_Environment');
        }

        if (!class_exists('Twig_Loader_Array')) {
            class_alias('Twig\Loader\ArrayLoader', 'Twig_Loader_Array');
        }


        $this->accessor = $accessor = $this->createMock('Finite\State\Accessor\StateAccessorInterface');
        $this->env = new \Twig_Environment(
            new \Twig_Loader_Array(
                array(
                    'state'       => '{{ finite_state(object) }}',
                    'transitions' => '{% for transition in finite_transitions(object) %}{{ transition }}{% endfor %}',
                    'properties'  => '{% for property, val in finite_properties(object) %}{{ property }}{% endfor %}',
                    'has'         => '{{ finite_has(object, property) ? "yes" : "no" }}',
                    'can'         => '{{ finite_can(object, transition, "foo") ? "yes" : "no" }}'
                )
            )
        );

        $container = new \Pimple(array(
            'state_machine' => function() use ($accessor) {
                $sm =  new StateMachine(null, null, $accessor);
                $sm->addState(new State('s1', State::TYPE_INITIAL, array(), array('foo' => true, 'bar' => false)));
                $sm->addTransition('t12', 's1', 's2');
                $sm->addTransition('t23', 's2', 's3');

                return $sm;
            }
        ));

        $this->context = new Context(new PimpleFactory($container, 'state_machine'));;
        $this->env->addExtension(new FiniteExtension($this->context));
    }

    public function testState()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('s1', $this->env->render('state', array('object' => $this->getObjectMock())));
    }

    public function testTransitions()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('t12', $this->env->render('transitions', array('object' => $this->getObjectMock())));
    }

    public function testProperties()
    {
        $this->accessor->expects($this->once())->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('foobar', $this->env->render('properties', array('object' => $this->getObjectMock())));
    }

    public function testHas()
    {
        $this->accessor->expects($this->exactly(2))->method('getState')->will($this->returnValue('s1'));
        $this->assertSame('yes', $this->env->render('has', array('object' => $this->getObjectMock(), 'property' => 'foo')));
        $this->assertSame('no', $this->env->render('has', array('object' => $this->getObjectMock(), 'property' => 'baz')));
    }

    public function testCan()
    {
        $this->assertSame('yes', $this->env->render('can', array('object' => $this->getObjectMock(), 'transition' => 't12')));
        $this->assertSame('no', $this->env->render('can', array('object' => $this->getObjectMock(), 'transition' => 't23')));
    }

    public function getObjectMock()
    {
        $mock = $this->createMock('Finite\StatefulInterface');

        return $mock;
    }
}
