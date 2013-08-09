<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Implement your document class
class Document implements Finite\StatefulInterface
{
    private $state;

    public function getFiniteState()
    {
        return $this->state;
    }

    public function setFiniteState($state)
    {
        $this->state = $state;
    }
}

// Configure your graph
$loader = new Finite\Loader\ArrayLoader([
    'class'  => 'Document',
    'states'  => [
        'draft' => [
            'type'       => Finite\State\StateInterface::TYPE_INITIAL,
            'properties' => ['deletable' => true, 'editable' => true],
        ],
        'proposed' => [
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => [],
        ],
        'accepted' => [
            'type'       => Finite\State\StateInterface::TYPE_FINAL,
            'properties' => ['printable' => true],
        ]
    ],
    'transitions' => [
        'propose' => ['from' => ['draft'], 'to' => 'proposed'],
        'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
        'reject'  => ['from' => ['proposed'], 'to' => 'draft'],
    ],
]);

$document = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader->load($stateMachine);
$stateMachine->initialize();


// Working with workflow

// Current state
var_dump($stateMachine->getCurrentState()->getName());
var_dump($stateMachine->getCurrentState()->getProperties());
var_dump($stateMachine->getCurrentState()->has('deletable'));
var_dump($stateMachine->getCurrentState()->has('printable'));

// Available transitions
var_dump($stateMachine->getCurrentState()->getTransitions());
var_dump($stateMachine->getCurrentState()->can('propose'));
var_dump($stateMachine->getCurrentState()->can('accept'));

// Apply transitions
try {
    $stateMachine->apply('accept');
} catch (\Finite\Exception\StateException $e) {
    echo $e->getMessage(), "\n";
}

// Applying a transition
$stateMachine->apply('propose');
var_dump($stateMachine->getCurrentState()->getName());
var_dump($document->getFiniteState());
