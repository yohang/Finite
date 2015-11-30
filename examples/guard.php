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

function pass_guard(\Finite\StateMachine\StateMachine $stateMachine)
{
    echo "Pass guard called\n";

    return true;
}

function fail_guard(\Finite\StateMachine\StateMachine $stateMachine)
{
    echo "Fail guard called\n";

    return false;
}

// Configure your graph
$document     = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader       = new Finite\Loader\ArrayLoader(array(
    'class'  => 'Document',
    'states'  => array(
        'draft' => array(
            'type'       => Finite\State\StateInterface::TYPE_INITIAL,
            'properties' => array(),
        ),
        'proposed' => array(
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => array(),
        ),
        'accepted' => array(
            'type'       => Finite\State\StateInterface::TYPE_FINAL,
            'properties' => array(),
        )
    ),
    'transitions' => array(
        'propose' => array('from' => array('draft'), 'to' => 'proposed', 'guard' => 'pass_guard'),
        'accept'  => array('from' => array('proposed'), 'to' => 'accepted', 'guard' => 'fail_guard'),
    ),
));

$loader->load($stateMachine);
$stateMachine->initialize();


// testing the guard
echo "Can we apply propose ? \n";
var_dump($stateMachine->can('propose'));
$stateMachine->apply('propose');

echo "\nCan we apply accept ? \n";
var_dump($stateMachine->can('accept'));
