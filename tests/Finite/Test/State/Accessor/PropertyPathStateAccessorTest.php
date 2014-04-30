<?php

namespace Finite\Test\State\Accessor;

use Finite\State\Accessor\PropertyPathStateAccessor;

class PropertyPathStateAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyAccessor;

    protected function setUp()
    {
        $this->propertyAccessor = $this->getMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
    }

    public function testGetState()
    {
        $object   = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->getMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with($stateful, 'bar')
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $object->getState($stateful));

        $object = new PropertyPathStateAccessor('finiteState', $this->propertyAccessor);
        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with($stateful, 'finiteState')
            ->will($this->returnValue('foo'));

        $this->assertSame('foo', $object->getState($stateful));
    }

    public function testSetState()
    {
        $object   = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->getMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with($stateful, 'bar', 'foo')
            ->will($this->returnValue('foo'));

        $object->setState($stateful, 'foo');

        $object = new PropertyPathStateAccessor('finiteState', $this->propertyAccessor);
        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with($stateful, 'finiteState')
            ->will($this->returnValue('foo'));

        $object->setState($stateful, 'foo');
    }
}
