<?php

namespace Finite\Test\Loader;

use Finite\Loader\ArrayLoader;
use Finite\StateMachine\StateMachine;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Yohan Giarelli <yohan@frequence-web.fr>
 */
class ArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayLoader
     */
    protected $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $callbackHandler;

    protected function setUp()
    {
        $this->callbackHandler = $this->getMockBuilder('Finite\Event\CallbackHandler')
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new ArrayLoader(
            array(
                'class'         => 'Stateful1',
                'states'        => array(
                    'start'  => array('type' => 'initial', 'properties' => array('foo' => true, 'bar' => false)),
                    'middle' => array('type' => 'normal', 'properties' => array()),
                    'end'    => array('type' => 'final', 'properties' => array()),
                ),
                'transitions'   => array(
                    'middleize' => array(
                        'from' => array('start'),
                        'to'   => 'middle'
                    ),
                    'finish'    => array(
                        'from' => array('middle'),
                        'to'   => 'end'
                    )
                )
            ),
            $this->callbackHandler
        );
    }

    public function testLoad()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachine');
        $sm->expects($this->once())->method('setStateAccessor');
        $sm->expects($this->once())->method('setGraph');
        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testLoadGraph()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachine');

        $graphName = 'foobar';
        $loader = new ArrayLoader(array('class' => 'Stateful1', 'graph' => $graphName), $this->callbackHandler);

        $sm->expects($this->once())->method('setGraph')->with($graphName);

        $loader->load($sm);
    }

    public function testLoadWithMissingOptions()
    {
        $sm = $this->createMock('Finite\StateMachine\StateMachine');

        $this->object = new ArrayLoader(
            array(
                'class'       => 'Stateful1',
                'states'      => array(
                    'start'  => array('type' => 'initial', 'properties' => array('foo' => true, 'bar' => false)),
                    'middle' => array(),
                    'end'    => array('type' => 'final'),
                ),
                'transitions' => array(
                    'middleize' => array(
                        'from' => 'start',
                        'to'   => 'middle'
                    ),
                    'finish'    => array(
                        'from' => array('middle'),
                        'to'   => 'end'
                    )
                ),
            ),
            $this->callbackHandler
        );

        $sm->expects($this->exactly(3))->method('addState');
        $sm->expects($this->exactly(2))->method('addTransition');
        $this->object->load($sm);
    }

    public function testLoadCallbacks()
    {
        $sm                         = $this->createMock('Finite\StateMachine\StateMachine');
        $allTimes                   = function () {};
        $beforeMiddleize            = function () {};
        $fromStartToOtherThanMiddle = function () {};

        $this->object = new ArrayLoader(
            array(
                'class'       => 'Stateful1',
                'states'      => array(
                    'start'  => array('type' => 'initial'),
                    'middle' => array(),
                    'end'    => array('type' => 'final'),
                ),
                'transitions' => array(
                    'middleize' => array('from' => 'start', 'to' => 'middle'),
                    'finish'    => array('from' => array('middle'), 'to' => 'end')
                ),
                'callbacks'   => array(
                    'before' => array(
                        array('on' => 'middleize', 'do' => $beforeMiddleize),
                        array('from' => 'start', 'to' => '-middle', 'do' => $fromStartToOtherThanMiddle)
                    ),
                    'after'  => array(
                        array('do' => $allTimes)
                    )
                )
            ),
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
            array(
                'class'       => 'Stateful1',
                'states'      => array(
                    'start'  => array('type' => 'initial', 'properties' => array('foo' => true, 'bar' => false)),
                    'end'    => array('type' => 'final'),
                ),
                'transitions' => array(
                    'finish'    => array(
                        'from' => array('middle'),
                        'to'   => 'end',
                        'properties' => array('default' => 'default'),
                        'configure_properties' => function (OptionsResolver $optionsResolver) {
                            $optionsResolver->setRequired('required');
                        },
                    )
                ),
            ),
            $this->callbackHandler
        );

        $this->object->load($sm);
    }

    public function testLoadWithCustomStateAccessor()
    {
        $sa = $this->getMockBuilder('Finite\State\Accessor\PropertyPathStateAccessor')
            ->setMockClassName('CustomAccessor')
            ->getMock();

        $sm = new StateMachine;
        $sm->setStateAccessor($sa);

        $this->object->load($sm);

        $this->assertAttributeInstanceOf('CustomAccessor', 'stateAccessor', $sm);
    }

    public function testSupports()
    {
        $object = $this->getMockBuilder('Finite\StatefulInterface')
            ->setMockClassName('Stateful1')
            ->getMock();
        $object2 = $this->getMockBuilder('Finite\StatefulInterface')
            ->setMockClassName('Stateful2')
            ->getMock();

        $this->assertTrue($this->object->supports($object));
        $this->assertFalse($this->object->supports($object2));

        $alternativeLoader = new ArrayLoader(array('class' => 'Stateful1', 'graph' => 'foobar'));
        $this->assertTrue($alternativeLoader->supports($object, 'foobar'));
        $this->assertFalse($alternativeLoader->supports($object));
    }
}
