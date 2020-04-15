<?php

namespace Finite\Test\Bundle\FiniteBundle\DependencyInjection;

use Finite\Bundle\FiniteBundle\DependencyInjection\FiniteFiniteExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Test of FiniteExtension
 *
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class FiniteFiniteExtensionTest extends TestCase
{
    /**
     * @var FiniteFiniteExtension
     */
    protected $object;

    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected function setUp(): void
    {
        $this->object = new FiniteFiniteExtension;
        $this->container = new ContainerBuilder;
        $this->object->load($this->getConfig(), $this->container);
    }

    public function testServicesSetUp(): void
    {
        $this->assertTrue($this->container->has('finite.factory'));
        $this->assertTrue($this->container->has('finite.state_machine'));
        $this->assertFalse($this->container->getDefinition('finite.state_machine')->isShared());
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

    private function getExpectedConfig(): array
    {
        return [
            'class' => 'Stateful1',
            'graph' => 'default',
            'property_path' => 'finiteState',
            'states' => [
                'state1' => [
                    'type' => 'initial',
                    'properties' => [],
                ],
                'state2' => [
                    'type' => 'normal',
                    'properties' => [],
                ],
                'state3' => [
                    'type' => 'final',
                    'properties' => [
                        'foo' => true,
                        'bar' => false,
                    ],
                ],
            ],
            'transitions' => [
                '1_to_2' => [
                    'from' => ['state1'],
                    'to' => 'state2',
                    'properties' => [],
                ],
                '2_to_3' => [
                    'from' => ['state2'],
                    'to' => 'state3',
                    'properties' => ['foo' => 'bar'],
                ],
            ],
            'callbacks' => [
                'before' => [
                    'callback1' => ['on' => '1_to_2', 'do' => ['@my.listener.service', 'on1To2']],
                ],
                'after' => [
                    'callback2' => ['from' => '-state3', 'to' => ['state2', 'state3'], 'do' => ['@my.listener.service', 'on1To2']],
                ],
            ],
        ];
    }

    private function getConfig(): array
    {
        return [
            'finite_finite' => [
                'workflow1' => [
                    'class' => 'Stateful1',
                    'states' => [
                        'state1' => ['type' => 'initial'],
                        'state2' => ['type' => 'normal'],
                        'state3' => [
                            'type' => 'final',
                            'properties' => [
                                'foo' => true,
                                'bar' => false,
                            ],
                        ],
                    ],
                    'transitions' => [
                        '1_to_2' => [
                            'from' => ['state1'],
                            'to' => 'state2',
                        ],
                        '2_to_3' => [
                            'from' => ['state2'],
                            'to' => 'state3',
                            'properties' => ['foo' => 'bar'],
                        ],
                    ],
                    'callbacks' => [
                        'before' => [
                            'callback1' => ['on' => '1_to_2', 'do' => ['@my.listener.service', 'on1To2']],
                        ],
                        'after' => [
                            'callback2' => ['from' => '-state3', 'to' => ['state2', 'state3'], 'do' => ['@my.listener.service', 'on1To2']],
                            'callback3' => ['disabled' => true],
                        ],
                    ],
                ],
            ],
        ];
    }
}
