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

// (Optional) Create a factory
$pimple = new Pimple(array(
        'finite.state_machine' => function () {
            return new \Finite\StateMachine\StateMachine;
        }
));
$factory = new \Finite\Factory\PimpleFactory($pimple, 'finite.state_machine');

// Configure your graph
$document     = new Document;
$stateMachine = new Finite\StateMachine\StateMachine($document);
$loader       = new Finite\Loader\ArrayLoader(array(
    'class'       => 'Document',
    'states'      => array(
        'draft'    => array(
            'type'       => Finite\State\StateInterface::TYPE_INITIAL,
            'properties' => array('deletable' => true, 'editable' => true),
        ),
        'proposed' => array(
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => array(),
        ),
        'accepted' => array(
            'type'       => Finite\State\StateInterface::TYPE_NORMAL,
            'properties' => array('printable' => true),
        ),
        'archived' => array(
            'type'       => Finite\State\StateInterface::TYPE_FINAL,
            'properties' => array(),
        )
    ),
    'transitions' => array(
        'propose' => array('from' => array('draft'), 'to' => 'proposed'),
        'accept'  => array('from' => array('proposed'), 'to' => 'accepted'),
        'reject'  => array('from' => array('proposed'), 'to' => 'draft'),
        'archive' => array('from' => ['accepted'], 'to' => 'archived'),
    ),
    'callbacks' => array(
        'before' => array(
            array(
                'from' => '-proposed',
                'do' => function(Finite\StatefulInterface $document, \Finite\Event\TransitionEvent $e) {
                    echo 'Applying transition '.$e->getTransition()->getName(), "\n";
                }
            ),
            array(
                'from' => 'proposed',
                'do' => function() {
                    echo 'Applying transition from proposed state', "\n";
                }
            )
        ),
        'after' => array(
            array(
                'to' => array('accepted'), 'do' => array($document, 'display')
            ),
            array(
                'to' => array('accepted'),
                'do' => array(new \Finite\Callback\CascadeTransitionCallback($factory), 'applySelf'),
                'args' => array('archive')
            )
        )
    )
));

$loader->load($stateMachine);
$stateMachine->initialize();

// (Optional) Register the loader in the factory
$factory->addLoader($loader);

$stateMachine->getDispatcher()->addListener('finite.pre_transition', function(\Finite\Event\TransitionEvent $e) {
    echo 'This is a pre transition', "\n";
});

$stateMachine->apply('propose');
$stateMachine->apply('reject');
$stateMachine->apply('propose');
$stateMachine->apply('accept');
