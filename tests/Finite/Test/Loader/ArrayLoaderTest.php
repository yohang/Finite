<?php

namespace Finite\Test\Loader;

use Finite\Event\CallbackHandler;
use Finite\Loader\ArrayLoader;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Yohan Giarelli <yohan@giarel.li>
 */
class ArrayLoaderTest extends TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $object;

    /**
     * @var MockObject
     */
    protected $callbackHandler;

    protected function setUp(): void
    {
        $this->callbackHandler = $this
            ->getMockBuilder(CallbackHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new ArrayLoader(
            [
                'class'       => 'Stateful1',
                'states'      => [
                    'start'  => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'middle' => ['type' => 'normal', 'properties' => []],
                    'end'    => ['type' => 'final', 'properties' => []],
                ],
                'transitions' => [
                    'middleize' => [
                        'from' => ['start'],
                        'to'   => 'middle',
                    ],
                    'finish'    => [
                        'from' => ['middle'],
                        'to'   => 'end',
                    ],
                ],
            ],
            $this->callbackHandler
        );
    }

    public function testLoad()
    {
        $sm = $this->createMock(StateMachine::class);
        $sm->expects($this->once())->method('setStateAccessor');
        $sm->expects($this->once())->method('setGraph');
        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');

        $this->object->load($sm);
    }

    public function testLoadGraph()
    {
        $sm = $this->createMock(StateMachine::class);

        $graphName = 'foobar';
        $loader    = new ArrayLoader(['class' => 'Stateful1', 'graph' => $graphName], $this->callbackHandler);

        $sm->expects($this->once())->method('setGraph')->with($graphName);

        $loader->load($sm);

        $this->assertSame('Stateful1', $loader->getClassName());
        $this->assertSame($graphName, $loader->getGraphName());
    }

    public function testLoadWithMissingOptions()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachine');

        $this->object = new ArrayLoader(
            [
                'class'       => 'Stateful1',
                'states'      => [
                    'start'  => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'middle' => [],
                    'end'    => ['type' => 'final'],
                ],
                'transitions' => [
                    'middleize' => [
                        'from' => 'start',
                        'to'   => 'middle',
                    ],
                    'finish'    => [
                        'from' => ['middle'],
                        'to'   => 'end',
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testLoadCallbacks()
    {
        $sm                         = $this->createMock('Finite\StateMachine\StateMachine');
        $allTimes                   = function () {
        };
        $beforeMiddleize            = function () {
        };
        $fromStartToOtherThanMiddle = function () {
        };

        $this->object = new ArrayLoader(
            [
                'class'       => 'Stateful1',
                'states'      => [
                    'start'  => ['type' => 'initial'],
                    'middle' => [],
                    'end'    => ['type' => 'final'],
                ],
                'transitions' => [
                    'middleize' => ['from' => 'start', 'to' => 'middle'],
                    'finish'    => ['from' => ['middle'], 'to' => 'end'],
                ],
                'callbacks'   => [
                    'before' => [
                        ['on' => 'middleize', 'do' => $beforeMiddleize],
                        ['from' => 'start', 'to' => '-middle', 'do' => $fromStartToOtherThanMiddle],
                    ],
                    'after'  => [
                        ['do' => $allTimes],
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $this->callbackHandler
            ->expects($this->at(0))
            ->method('addBefore')
            ->with($this->isInstanceOf('Finite\Event\Callback\Callback'));

        $this->callbackHandler
            ->expects($this->at(1))
            ->method('addBefore')
            ->with($this->isInstanceOf('Finite\Event\Callback\Callback'));

        $this->callbackHandler
            ->expects($this->at(2))
            ->method('addAfter')
            ->with($this->isInstanceOf('Finite\Event\Callback\Callback'));

        $this->object->load($sm);
    }

    public function testLoadWithProperties()
    {
        $sm = new StateMachine();

        $this->object = new ArrayLoader(
            [
                'class'       => 'Stateful1',
                'states'      => [
                    'start' => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'end'   => ['type' => 'final'],
                ],
                'transitions' => [
                    'finish' => [
                        'from'                 => ['middle'],
                        'to'                   => 'end',
                        'properties'           => ['default' => 'default'],
                        'configure_properties' => function (OptionsResolver $optionsResolver) {
                            $optionsResolver->setRequired('required');
                        },
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $this->object->load($sm);


        // Used for PHPUnit not to consider the tesk risky (No exception is OK)
        $this->assertTrue(true);
    }

    public function testSupports()
    {
        $object  = $this
            ->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful1')
            ->getMock();

        $object2 = $this
            ->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful2')
            ->getMock();

        $this->assertTrue($this->object->supports($object));
        $this->assertFalse($this->object->supports($object2));

        $alternativeLoader = new ArrayLoader(['class' => 'Stateful1', 'graph' => 'foobar']);
        $this->assertTrue($alternativeLoader->supports($object, 'foobar'));
        $this->assertFalse($alternativeLoader->supports($object));
    }

    public function testSupportsObject()
    {
        $object  = $this
            ->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful1')
            ->getMock();

        $object2 = $this
            ->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful2')
            ->getMock();

        $this->assertTrue($this->object->supportsObject($object));
        $this->assertFalse($this->object->supportsObject($object2));

        $alternativeLoader = new ArrayLoader(['class' => 'Stateful1', 'graph' => 'foobar']);

        $this->assertTrue($alternativeLoader->supportsObject($object));
        $this->assertTrue($alternativeLoader->supportsObject($object));
    }
}
