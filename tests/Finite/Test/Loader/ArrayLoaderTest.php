<?php

namespace Finite\Test\Loader;

use Finite\Event\Callback\Callback;
use Finite\Event\CallbackHandler;
use Finite\Loader\ArrayLoader;
use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ArrayLoaderTest extends TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $object;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $callbackHandler;

    protected function setUp(): void
    {
        $this->callbackHandler = $this->getMockBuilder(CallbackHandler::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->object = new ArrayLoader(
            [
                'class' => 'Stateful1',
                'states' => [
                    'start' => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'middle' => ['type' => 'normal', 'properties' => []],
                    'end' => ['type' => 'final', 'properties' => []],
                ],
                'transitions' => [
                    'middleize' => [
                        'from' => ['start'],
                        'to' => 'middle',
                    ],
                    'finish' => [
                        'from' => ['middle'],
                        'to' => 'end',
                    ],
                ],
            ],
            $this->callbackHandler
        );
    }

    public function testLoad(): void
    {
        $sm = $this->createMock(StateMachine::class);
        $sm->expects($this->once())->method('setStateAccessor');
        $sm->expects($this->once())->method('setGraph');
        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testLoadGraph(): void
    {
        $sm = $this->createMock(StateMachine::class);

        $graphName = 'foobar';
        $loader = new ArrayLoader(['class' => 'Stateful1', 'graph' => $graphName], $this->callbackHandler);

        $sm->expects($this->once())->method('setGraph')->with(...[$graphName]);

        $loader->load($sm);
    }

    public function testLoadWithMissingOptions(): void
    {
        $sm = $this->createMock(StateMachine::class);

        $this->object = new ArrayLoader(
            [
                'class' => 'Stateful1',
                'states' => [
                    'start' => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'middle' => [],
                    'end' => ['type' => 'final'],
                ],
                'transitions' => [
                    'middleize' => [
                        'from' => 'start',
                        'to' => 'middle',
                    ],
                    'finish' => [
                        'from' => ['middle'],
                        'to' => 'end',
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testLoadCallbacks(): void
    {
        $sm = $this->createMock(StateMachine::class);
        $allTimes = static function () {
        };
        $beforeMiddleize = static function () {
        };
        $fromStartToOtherThanMiddle = static function () {
        };

        $this->object = new ArrayLoader(
            [
                'class' => 'Stateful1',
                'states' => [
                    'start' => ['type' => 'initial'],
                    'middle' => [],
                    'end' => ['type' => 'final'],
                ],
                'transitions' => [
                    'middleize' => ['from' => 'start', 'to' => 'middle'],
                    'finish' => ['from' => ['middle'], 'to' => 'end'],
                ],
                'callbacks' => [
                    'before' => [
                        ['on' => 'middleize', 'do' => $beforeMiddleize],
                        ['from' => 'start', 'to' => '-middle', 'do' => $fromStartToOtherThanMiddle],
                    ],
                    'after' => [
                        ['do' => $allTimes],
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $this->callbackHandler
            ->expects($this->at(0))
            ->method('addBefore')
            ->with(...[$this->isInstanceOf(Callback::class)])
        ;

        $this->callbackHandler
            ->expects($this->at(1))
            ->method('addBefore')
            ->with(...[$this->isInstanceOf(Callback::class)])
        ;

        $this->callbackHandler
            ->expects($this->at(2))
            ->method('addAfter')
            ->with(...[$this->isInstanceOf(Callback::class)])
        ;

        $this->object->load($sm);
    }

    public function testLoadWithProperties(): void
    {
        $sm = new StateMachine();

        $this->object = new ArrayLoader(
            [
                'class' => 'Stateful1',
                'states' => [
                    'start' => ['type' => 'initial', 'properties' => ['foo' => true, 'bar' => false]],
                    'end' => ['type' => 'final'],
                ],
                'transitions' => [
                    'finish' => [
                        'from' => ['middle'],
                        'to' => 'end',
                        'properties' => ['default' => 'default'],
                        'configure_properties' => static function (OptionsResolver $optionsResolver) {
                            $optionsResolver->setRequired('required');
                        },
                    ],
                ],
            ],
            $this->callbackHandler
        );

        $this->object->load($sm);

        $this->assertCount(1, $sm->getTransitions());
        $this->assertCount(3, $sm->getStates());
    }

    /**
     * @covers \Finite\StateMachine\StateMachine::getStateAccessor
     */
    public function testLoadWithCustomStateAccessor(): void
    {
        $sa = $this->getMockBuilder(PropertyPathStateAccessor::class)
            ->setMockClassName('CustomAccessor')
            ->getMock()
        ;

        $sm = new StateMachine;
        $sm->setStateAccessor($sa);

        $this->object->load($sm);

        $this->assertInstanceOf('CustomAccessor', $sm->getStateAccessor());
    }

    public function testSupports(): void
    {
        $object = $this->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful1')
            ->getMock()
        ;
        $object2 = $this->getMockBuilder(StatefulInterface::class)
            ->setMockClassName('Stateful2')
            ->getMock()
        ;

        $this->assertTrue($this->object->supports($object));
        $this->assertFalse($this->object->supports($object2));

        $alternativeLoader = new ArrayLoader(['class' => 'Stateful1', 'graph' => 'foobar']);
        $this->assertTrue($alternativeLoader->supports($object, 'foobar'));
        $this->assertFalse($alternativeLoader->supports($object));
    }
}
