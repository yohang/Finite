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
$loader       = new Finite\Loader\ArrayLoader(array(
    'class'       => 'Document',
    'states'      => array(
        'draft'    => array(
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
        'propose' => array('from' => array('draft'), 'to' => 'proposed'),
        'accept'  => array('from' => array('proposed'), 'to' => 'accepted', 'properties' => ['count' => 0]),
        'reject'  => array(
            'from' => array('proposed'),
            'to' => 'draft',
            'configure_properties' => function(\Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver) {
                $optionsResolver->setRequired('count');
            }
        ),
    ),
    'callbacks' => array(
        'before' => array(
            array(
                'do' => function(Finite\StatefulInterface $document, \Finite\Event\TransitionEvent $e) {
                    echo sprintf(
                        "Applying transition \"%s\", count is \"%s\"\n",
                        $e->getTransition()->getName(),
                        $e->get('count', 'undefined')
                    );
                }
            )
        )
    )
));

$loader->load($stateMachine);
$stateMachine->initialize();

try {
    // Trying with an undefined property
    $stateMachine->apply('propose', ['count' => 1]);
} catch (\Finite\Exception\TransitionException $e) {
    echo "Property \"propose\" does not exists.\n";
}
$stateMachine->apply('propose');

try {
    // Trying without a mandatory property
    $stateMachine->apply('reject');
} catch (\Finite\Exception\TransitionException $e) {
    echo "Property \"count\" is mandatory.\n";
}
$stateMachine->apply('reject',  ['count' => 2]);

$stateMachine->apply('propose');

// Default value is used
$stateMachine->apply('accept');
