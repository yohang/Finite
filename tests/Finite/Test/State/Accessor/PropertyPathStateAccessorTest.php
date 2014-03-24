<?php

namespace Finite\Test\State\Accessor;

use Finite\State\Accessor\PropertyPathStateAccessor;

class PropertyPathStateAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyAccessor;

    /**
     * @var PropertyPathStateAccessor
     */
    protected $object;

    protected function setUp()
    {
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
        $this->object           = new PropertyPathStateAccessor($this->propertyAccessor);
    }

    public function testGetState()
    {
        $stateful = $this->getMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with($stateful, 'bar')
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $this->object->getState($stateful, 'bar'));

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with($stateful, 'finiteState')
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $this->object->getState($stateful));
    }

    public function testSetState()
    {
        $stateful = $this->getMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with($stateful, 'bar', 'foo')
            ->will($this->returnValue('foo'));

        $this->object->setState($stateful, 'foo', 'bar');

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with($stateful, 'finiteState')
            ->will($this->returnValue('foo'));

        $this->object->setState($stateful, 'foo');
    }
}
