<?php

require_once __DIR__ . '/../vendor/autoload.php';

class Order
{
    // First state graph, payment status
    const PAYMENT_PENDING  = 'pending';
    const PAYMENT_ACCEPTED = 'accepted';
    const PAYMENT_REFUSED  = 'refused';

    // second state graph, shipping status
    const SHIPPING_PENDING = 'pending';
    const SHIPPING_PARTIAL = 'partial';
    const SHIPPING_SHIPPED = 'shipped';

    private $paymentStatus;
    private $shippingStatus;

    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function setShippingStatus($shippingStatus)
    {
        $this->shippingStatus = $shippingStatus;
    }

    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }
}

$order = new Order;

// Configure the payment graph
$paymentStateMachine = new Finite\StateMachine\StateMachine($order);
$paymentLoader       = new Finite\Loader\ArrayLoader([
    'class'         => 'Order',
    'property_path' => 'paymentStatus',
    'states'        => [
        Order::PAYMENT_PENDING  => ['type' => Finite\State\StateInterface::TYPE_INITIAL],
        Order::PAYMENT_ACCEPTED => ['type' => Finite\State\StateInterface::TYPE_FINAL],
        Order::PAYMENT_REFUSED  => ['type' => Finite\State\StateInterface::TYPE_FINAL],
    ],
    'transitions'   => [
        'accept' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_ACCEPTED],
        'refuse' => ['from' => [Order::PAYMENT_PENDING], 'to' => Order::PAYMENT_REFUSED],
    ],
]);

$paymentLoader->load($paymentStateMachine);
$paymentStateMachine->initialize();

// Configure the shipping graph
$shippingStateMachine = new Finite\StateMachine\StateMachine($order);
$shippingLoader       = new Finite\Loader\ArrayLoader([
    'class'         => 'Order',
    'property_path' => 'shippingStatus',
    'states'        => [
        Order::SHIPPING_PENDING => ['type' => Finite\State\StateInterface::TYPE_INITIAL],
        Order::SHIPPING_PARTIAL => ['type' => Finite\State\StateInterface::TYPE_NORMAL],
        Order::SHIPPING_SHIPPED => ['type' => Finite\State\StateInterface::TYPE_FINAL],
    ],
    'transitions'   => [
        'ship_partially' => ['from' => [Order::SHIPPING_PENDING], 'to' => Order::SHIPPING_PARTIAL],
        'ship'           => ['from' => [Order::SHIPPING_PENDING, Order::SHIPPING_PARTIAL], 'to' => Order::SHIPPING_SHIPPED],
    ],
]);

$shippingLoader->load($shippingStateMachine);
$shippingStateMachine->initialize();


// Working with workflows

// Current state
var_dump($paymentStateMachine->getCurrentState()->getName());
var_dump($paymentStateMachine->getCurrentState()->getProperties());

// Available transitions
var_dump($paymentStateMachine->getCurrentState()->getTransitions());
var_dump($paymentStateMachine->can('accept'));
$paymentStateMachine->apply('accept');
var_dump($paymentStateMachine->getCurrentState()->getName());

