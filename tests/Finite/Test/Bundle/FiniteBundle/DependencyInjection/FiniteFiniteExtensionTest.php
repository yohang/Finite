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
        $config = $this->getConfig();

        $this->assertTrue($this->container->has('finite.factory'));
        $this->assertTrue($this->container->has('finite.state_machine'));
        $this->assertSame('prototype', $this->container->getDefinition('finite.state_machine')->getScope());
        $this->assertFalse($this->container->hasDefinition('finite.array_loader'));
        $this->assertTrue($this->container->hasDefinition('finite.loader.workflow1'));
        $this->assertTrue($this->container->hasDefinition('finite.context'));
        $this->assertTrue($this->container->hasDefinition('finite.twig_extension'));

        $this->assertEquals(
            $config['finite_finite']['workflow1'],
            $this->container->getDefinition('finite.loader.workflow1')->getArgument(0)
        );
    }

    private function getConfig()
    {
        return array(
            'finite_finite' => array(
                'workflow1' => array(
                    'class'  => 'Stateful1',
                    'states' => array(
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
                    'transitions' => array(
                        '1_to_2' => array(
                            'from' => array('state1'),
                            'to'   => 'state2'
                        ),
                        '2_to_3' => array(
                            'from' => array('state2'),
                            'to'   => 'state3'
                        ),
                    )
                )
            )
        );
    }
}
