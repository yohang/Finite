<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Finite\Loader\ArrayLoader;
use Finite\State\StateInterface;
use Finite\StatefulInterface;
use Finite\StateMachine\StateMachine;
use Finite\Visualisation\Configuration;
use Finite\Visualisation\Graphviz;

/**
 * Example implementation
 */
class Document implements StatefulInterface
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
$loader = new ArrayLoader([
    'class'  => 'Document',
    'states'  => [
        'draft' => [
            'type'       => StateInterface::TYPE_INITIAL,
            'properties' => ['deletable' => true, 'editable' => true],
        ],
        'proposed' => [
            'type'       => StateInterface::TYPE_NORMAL,
            'properties' => [],
        ],
        'accepted' => [
            'type'       => StateInterface::TYPE_NORMAL,
            'properties' => ['printable' => true],
        ],
        'published' => [
            'type'       => StateInterface::TYPE_FINAL,
            'properties' => ['printable' => true],
        ]
    ],
    'transitions' => [
        'propose' => ['from' => ['draft'],    'to' => 'proposed'],
        'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
        'reject'  => ['from' => ['proposed'], 'to' => 'draft'],
        'publish' => ['from' => ['accepted'], 'to' => 'published'],
        'cheat'   => ['from' => ['draft'],    'to' => 'published'],
    ],
]);

$document = new Document;
$stateMachine = new StateMachine($document);
$loader->load($stateMachine);
$stateMachine->initialize();

$config = new Configuration(__DIR__ . '/rendered-graph.png', true, 'red');
$renderer = new Graphviz($config);
$renderer->render($stateMachine);
