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
        $this->accessor = $accessor = $this->getMock('Finite\State\Accessor\StateAccessorInterface');
        $this->env = new \Twig_Environment(
            new \Twig_Loader_Array(
                array(
                    'state'       => '{{ finite_state(object) }}',
                    'transitions' => '{% for transition in finite_transitions(object) %}{{ transition }}{% endfor %}',
                    'properties'  => '{% for property, val in finite_properties(object) %}{{ property }}{% endfor %}',
                    'has'         => '{{ finite_has(object, property) ? "yes" : "no" }}'
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

    public function getObjectMock()
    {
        $mock = $this->getMock('Finite\StatefulInterface');

        return $mock;
    }
}
