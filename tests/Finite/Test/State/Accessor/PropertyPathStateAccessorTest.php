<?php

namespace Finite\Test\State\Accessor;

use Finite\State\Accessor\PropertyPathStateAccessor;
use Finite\StatefulInterface;
use PHPUnit_Framework_TestCase;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class PropertyPathStateAccessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $propertyAccessor;

    protected function setUp()
    {
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
    }

    /**
     * @throws \Finite\Exception\NoSuchPropertyException
     */
    public function testGetState()
    {
        $object = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with(...[$stateful, 'bar'])
            ->willReturn('foo')
        ;

        $this->assertSame('foo', $object->getState($stateful));

        $object = new PropertyPathStateAccessor('finiteState', $this->propertyAccessor);
        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('getValue')
            ->with(...[$stateful, 'finiteState'])
            ->willReturn('foo')
        ;

        $this->assertSame('foo', $object->getState($stateful));
    }

    /**
     * @throws \Finite\Exception\NoSuchPropertyException
     */
    public function testSetState()
    {
        $object = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with(...[$stateful, 'bar', 'foo'])
            ->willReturn('foo')
        ;

        $object->setState($stateful, 'foo');

        $object = new PropertyPathStateAccessor('finiteState', $this->propertyAccessor);
        $this->propertyAccessor
            ->expects($this->at(0))
            ->method('setValue')
            ->with(...[$stateful, 'finiteState'])
            ->willReturn('foo')
        ;

        $object->setState($stateful, 'foo');
    }

    /**
     * @throws \Finite\Exception\NoSuchPropertyException
     */
    public function testSetOnUnknownProperty()
    {
        $this->expectException(\Finite\Exception\NoSuchPropertyException::class);

        $object = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->propertyAccessor
            ->expects($this->once())
            ->method('setValue')
            ->with(...[$stateful, 'bar', 'foo'])
            ->willThrowException(new NoSuchPropertyException)
        ;

        $object->setState($stateful, 'foo');
    }

    /**
     * @throws \Finite\Exception\NoSuchPropertyException
     */
    public function testGetOnUnknownProperty()
    {
        $this->expectException(\Finite\Exception\NoSuchPropertyException::class);

        $object = new PropertyPathStateAccessor('bar', $this->propertyAccessor);
        $stateful = $this->createMock(StatefulInterface::class);

        $this->propertyAccessor
            ->expects($this->once())
            ->method('getValue')
            ->with(...[$stateful, 'bar'])
            ->willThrowException(new NoSuchPropertyException)
        ;

        $object->getState($stateful);
    }
}
