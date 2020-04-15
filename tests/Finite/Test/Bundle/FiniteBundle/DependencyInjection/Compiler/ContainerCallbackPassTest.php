<?php

namespace Finite\Test\Bundle\FiniteBundle\DependencyInjection\Compiler;

use Closure;
use Finite\Bundle\FiniteBundle\DependencyInjection\Compiler\ContainerCallbackPass;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Test of ContainerCallbackPass
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class ContainerCallbackPassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    protected function setUp()
    {
        $this->container = new ContainerBuilder;

        $this->load($this->container);
    }

    public function testPass()
    {
        $config = $this->container->getDefinition('loader')->getArgument(0);
        $this->assertEquals('@my_service', $config['callbacks']['before'][0]['do'][0]);

        $compilerPass = new ContainerCallbackPass();
        $compilerPass->process($this->container);

        $newConfig = $this->container->getDefinition('loader')->getArgument(0);
        $callback = $newConfig['callbacks']['before'][0]['do'][0];
        $this->assertInstanceOf(Reference::class, $callback);
        $this->assertEquals('my_service', (string)$callback);
        $this->assertEquals('not_a_service', $newConfig['callbacks']['before'][1]['do'][0]);
        $this->assertInstanceOf(Closure::class, $newConfig['callbacks']['after'][0]['do']);
    }

    private function load(ContainerBuilder $container)
    {
        $loader = new Definition();
        $loader->addTag('finite.loader');
        $loader->addArgument(
            [
                'callbacks' => [
                    'before' => [
                        [
                            'do' => ['@my_service', 'myMethod'],
                        ],
                        [
                            'do' => ['not_a_service', 'myMethod'],
                        ],
                    ],
                    'after' => [
                        [
                            'do' => static function () {
                                // Not a service
                            },
                        ],
                    ],
                ],
            ]
        );
        $container->setDefinition('loader', $loader);

        $service = new Definition('\Finite\Bundle\DependencyInjection\Compiler\ContainerCallbackPass');
        $container->setDefinition('my_service', $service);
    }
}
