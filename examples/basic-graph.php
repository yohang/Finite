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
$document     = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader       = new Finite\Loader\ArrayLoader(array(
    'class'  => 'Document',
    'states'  => array(
        'draft' => array(
            'type'       => Finite\State\StateInterface::TYPE_INITIAL,
            'properties' => array('deletable' => true, 'editable' => true),
        ),
        'proposed' => array(
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => array(),
        ),
        'accepted' => array(
            'type'       => Finite\State\StateInterface::TYPE_FINAL,
            'properties' => array('printable' => true),
        )
    ),
    'transitions' => array(
        'propose' => array('from' => array('draft'), 'to' => 'proposed'),
        'accept'  => array('from' => array('proposed'), 'to' => 'accepted'),
        'reject'  => array('from' => array('proposed'), 'to' => 'draft'),
    ),
));

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
var_dump($stateMachine->can('propose'));
var_dump($stateMachine->can('accept'));

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
