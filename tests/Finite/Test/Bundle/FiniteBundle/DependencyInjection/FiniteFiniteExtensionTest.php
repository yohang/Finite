<?php

namespace Finite\Test\Bundle\FiniteBundle\DependencyInjection;

use Finite\Bundle\FiniteBundle\DependencyInjection\FiniteFiniteExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test of FiniteExtension
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class FiniteFiniteExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FiniteFiniteExtension
     */
    protected $object;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected function setUp()
    {
        $this->object    = new FiniteFiniteExtension;
        $this->container = new ContainerBuilder;
        $this->object->load($this->getConfig(), $this->container);
    }

    public function testServicesSetUp()
    {
        $this->assertTrue($this->container->has('finite.factory'));
        $this->assertTrue($this->container->has('finite.state_machine'));

        if (method_exists($this->container->getDefinition('finite.state_machine'), 'isShared')) {
            $this->assertFalse($this->container->getDefinition('finite.state_machine')->isShared());
        }

        $this->assertFalse($this->container->hasDefinition('finite.array_loader'));
        $this->assertTrue($this->container->hasDefinition('finite.loader.workflow1'));
        $this->assertTrue($this->container->hasDefinition('finite.context'));
        $this->assertTrue($this->container->hasDefinition('finite.twig_extension'));
        $this->assertTrue($this->container->hasDefinition('finite.callback.handler'));
        $this->assertTrue($this->container->hasDefinition('finite.callback.builder.factory'));

        $this->assertTrue($this->container->getDefinition('finite.loader.workflow1')->isLazy());

        $this->assertEquals(
            $this->getExpectedConfig(),
            $this->container->getDefinition('finite.loader.workflow1')->getArgument(0)
        );
    }

    private function getExpectedConfig()
    {
        return array(
            'class'         => 'Stateful1',
            'graph'         => 'default',
            'property_path' => 'finiteState',
            'states'        => array(
                'state1' => array(
                    'type'       => 'initial',
                    'properties' => array()
                ),
                'state2' => array(
                    'type'       => 'normal',
                    'properties' => array()
                ),
                'state3' => array(
                    'type'       => 'final',
                    'properties' => array(
                        'foo' => true,
                        'bar' => false,
                    )
                )
            ),
            'transitions'   => array(
                '1_to_2' => array(
                    'from' => array('state1'),
                    'to'   => 'state2',
                    'properties' => array()
                ),
                '2_to_3' => array(
                    'from' => array('state2'),
                    'to'   => 'state3',
                    'properties' => array('foo' => 'bar')
                ),
            ),
            'callbacks'     => array(
                'before' => array(
                    'callback1' => array('on' => '1_to_2', 'do' => array('@my.listener.service', 'on1To2'))
                ),
                'after'  => array(
                    'callback2' => array('from' => '-state3', 'to' => array('state2', 'state3'), 'do' => array('@my.listener.service', 'on1To2'))
                )
            )
        );
    }

    private function getConfig()
    {
        return array(
            'finite_finite' => array(
                'workflow1' => array(
                    'class'       => 'Stateful1',
                    'states'      => array(
                        'state1' => array('type' => 'initial'),
                        'state2' => array('type' => 'normal'),
                        'state3' => array(
                            'type'       => 'final',
                            'properties' => array(
                                'foo' => true,
                                'bar' => false,
                            )
                        )
                    ),
                    'transitions' => array(
                        '1_to_2' => array(
                            'from' => array('state1'),
                            'to'   => 'state2'
                        ),
                        '2_to_3' => array(
                            'from' => array('state2'),
                            'to'   => 'state3',
                            'properties' => array('foo' => 'bar'),
                        ),
                    ),
                    'callbacks'   => array(
                        'before' => array(
                            'callback1' => array('on' => '1_to_2', 'do' => array('@my.listener.service', 'on1To2'))
                        ),
                        'after'  => array(
                            'callback2' => array('from' => '-state3', 'to' => array('state2', 'state3'), 'do' => array('@my.listener.service', 'on1To2')),
                            'callback3' => array('disabled' => true)
                        )
                    )
                )
            )
        );
    }
}
