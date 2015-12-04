Events / Callbacks
==================

Overview
--------

Finite use the Symfony EventDispatcher component to notify each actions done by the State Machine.

You can use the event system directly with callbacks in your configuration, or by attaching listeners
to the event dispatcher.


Implement your document class and define your workflow
------------------------------------------------------

See :doc:`basic_graph`.

Use callbacks
-------------

Callbacks can be defined directly in your State Machine configuration. The can be called
before or after the transition apply, and their definition use the following pattern :

.. code-block:: php

    <?php

    $definition = [
        'from' => [],  // a string or an array of string that represent the initial states that trigger the callback. Empty for All.
        'to'   => [],  // a string or an array of string that represent the target states that trigger the callback. Empty for All.
        'on'   => [],  // a string or an array of string that represent the transition names that trigger the callback. Empty for All.
        'do'   => function($object, Finite\Event\TransitionEvent $e) {
            // The callback
        }
    ];


`from` and `to` parameters can be any state names. Prefix by `-` to process an exclusion.
By default, callbacks matchs all the events.

Example :
^^^^^^^^^

.. code-block:: php

    <?php

    [
        'from' => ['all', '-proposed'],
        'do'   => function($object, Finite\Event\TransitionEvent $e) {
            // callback code
        }
    ];


Will match any transition that don't begin on the `proposed` state.

Full example :
^^^^^^^^^^^^^^

.. code-block:: php

    <?php

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
            'propose' => ['from' => ['draft'], 'to' => 'proposed', 'properties' => ['foo' => 'bar']],
            'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
            'reject'  => ['from' => ['proposed'], 'to' => 'draft'],
        ],
        'callbacks' => [
            'before' => [
                [
                    'from' => '-proposed',
                    'do'   => function(\Finite\Event\TransitionEvent $e) {
                        echo 'Applying transition '.$e->getTransition()->getName(), "\n";
                        if ($e->has('foo')) {
                            echo "Parameter \"foo\" is defined\n";
                        }
                    }
                ],
                [
                    'from' => 'proposed',
                    'do'   => function() {
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

    $stateMachine->apply('propose');
    // => "Applying transition propose"
    // => "Parameter "foo" is defined"

    $stateMachine->apply('reject');
    // => "Applying transition from proposed state"

    $stateMachine->apply('propose');
    // => "Applying transition propose"
    // => "Parameter "foo" is defined"

    $stateMachine->apply('accept');
    // => "Applying transition from proposed state"
    // => "Hello, I'm a document and I'm currently at the accepted state."


Use event dispatcher
--------------------

If you prefer, you can use directly the event dispatcher.

Here is the available events :

.. code-block:: text

    finite.initialize      => Dispatched at State Machine initialization
    finite.test_transition => Dispatched when testing if a transition can be applied
    finite.pre_transition  => Dispatched before a transition
    finite.post_transition => Dispatched after a transition

    finite.test_transition.{transitionName} => Dispatched when testing if a specific transition can be applied
    finite.pre_transition.{transitionName}  => Dispatched before a specific transition
    finite.post_transition.{transitionName} => Dispatched after a specific transition
    
    finite.test_transition.{graph}.{transitionName} => Dispatched when testing if a specific transition  in a specific graph can be applied
    finite.pre_transition.{graph}.{transitionName}  => Dispatched before a specific transition in a specific graph
    finite.post_transition.{graph}.{transitionName} => Dispatched after a specific transition in a specific graph


Example :
^^^^^^^^^

.. code-block:: php

    <?php

    $stateMachine->getDispatcher()->addListener('finite.pre_transition', function(\Finite\Event\TransitionEvent $e) {
        echo 'This is a pre transition', "\n";
    });
    $stateMachine->apply('propose');
    // => "This is a pre transition"

Example testing transitions:
^^^^^^^^^

.. code-block:: php

    <?php

    $stateMachine->getDispatcher()->addListener('finite.test_transition', function(\Finite\Event\TransitionEvent $e) {
        $e->reject();
    });
    
    try {
        $stateMachine->apply('propose');
    } 
    catch (Finite\StateMachine\Exception\StateException $e) {
        echo 'The transition did not apply', "\n";
    }
    
    // => "The transition did not apply"
