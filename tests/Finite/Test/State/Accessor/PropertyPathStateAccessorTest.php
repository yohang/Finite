<?php

namespace Finite\Test\State\Accessor;

use Finite\State\Accessor\PropertyPathStateAccessor;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

class PropertyPathStateAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyAccessor;

    protected function setUp()
    {
        $this->propertyAccessor = $this->createMock('Symfony\Component\PropertyAccess\PropertyAccessorInterface');
    }

    public function testGetState()
    {
        $object   = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock('Finite\StatefulInterface');

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
        $stateful = $this->createMock('Finite\StatefulInterface');

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

    /**
     * @expectedException \Finite\Exception\NoSuchPropertyException
     */
    public function testSetOnUnknownProperty()
    {
        $object   = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->once())
            ->method('setValue')
            ->with($stateful, 'bar', 'foo')
            ->will($this->throwException(new NoSuchPropertyException));


        $object->setState($stateful, 'foo');
    }

    /**
     * @expectedException \Finite\Exception\NoSuchPropertyException
     */
    public function testGetOnUnknownProperty()
    {
        $object   = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock('Finite\StatefulInterface');

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with($stateful, 'bar')
            ->will($this->throwException(new NoSuchPropertyException));


        $object->getState($stateful);
    }
}
