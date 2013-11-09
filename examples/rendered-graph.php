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
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => ['printable' => true],
        ],
        'published' => [
            'type'       => Finite\State\StateInterface::TYPE_FINAL,
            'properties' => ['printable' => true],
        ]
    ],
    'transitions' => [
        'propose' => ['from' => ['draft'], 'to' => 'proposed'],
        'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
        'reject'  => ['from' => ['proposed'], 'to' => 'draft'],
        'publish'  => ['from' => ['accepted'], 'to' => 'published'],
        'cheat'  => ['from' => ['draft'], 'to' => 'published'],
    ],
]);

$document = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader->load($stateMachine);
$stateMachine->initialize();

$renderer = new \Finite\Visualisation\Graphviz();
$renderer->render($stateMachine, __DIR__ . '/rendered-graph.png');