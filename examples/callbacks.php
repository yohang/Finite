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

    public function display()
    {
        echo 'Hello, I\'m a document and I\'m currently at the ', $this->state, ' state.', "\n";
    }
}

// Configure your graph
$document     = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader       = new Finite\Loader\ArrayLoader([
    'class'       => 'Document',
    'states'      => [
        'draft'    => [
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
    'callbacks' => [
        'before' => [
            [
                'from' => '-proposed',
                'do' => function(\Finite\Event\TransitionEvent $e) {
                    echo 'Applying transition '.$e->getTransition()->getName(), "\n";
                }
            ],
            [
                'from' => 'proposed',
                'do' => function() {
                    echo 'Applying transition from proposed state', "\n";
                }
            ]
        ],
        'after' => [
            [
                'to' => ['accepted'], 'do' => [$document, 'display']
            ]
        ]
    ]
]);

$loader->load($stateMachine);
$stateMachine->initialize();

$stateMachine->getDispatcher()->addListener('finite.pre_transition', function(\Finite\Event\TransitionEvent $e) {
    echo 'This is a pre transition', "\n";
});

$stateMachine->apply('propose');
$stateMachine->apply('reject');
$stateMachine->apply('propose');
$stateMachine->apply('accept');
