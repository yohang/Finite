Use with Symfony
================

Installation
------------

.. code-block:: bash

    $ composer require yohang/finite

Register the bundle
^^^^^^^^^^^^^^^^^^^

Register the bundle in your AppKernel:

.. code-block:: php

    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new\Finite\Bundle\FiniteBundle\FiniteFiniteBundle(),
            // ...
        );
    }

Defining your stateful class
----------------------------

As we want to track the state of an object (or the multiples states, but this example will focus on
object with single state-graph), create your class if it doesn't already exists. This class has simply
to implements ``Finite\StatefulInterface``.

This part is covered in :ref:`index_define_your_object`.


Configuration
-------------

.. code-block:: php

    finite_finite:

        document_workflow:
            class: MyDocument  # You class FQCN
            states:

                draft:    { type: initial, properties: { visible: false } }
                proposed: { type: normal,  properties: { visible: false } }
                accepted: { type: final,   properties: { visible: true  } }
                refused:  { type: final,   properties: { visible: false } }

            transitions:
                propose:  { from: draft,    to: proposed }
                accept:   { from: proposed, to: accepted }
                refuse:   { from: proposed, to: refused  }


At this point, your graph is ready and you can start using your workflow on your object.

Controller / Service usage
--------------------------

Finite define several services into the Symfony DIC. The easier to use is ``finite.context``.

Example
^^^^^^^

.. code-block:: php

    <?php

    $context = $this->get('finite.context');
    $context->getState($document); // return "draft", or… the current state if different
    $context->getProperties($document); // array:1 [ 'visible' => false ]
    $context->getTransitions($document); // array:2 [ 0 => "propose", 1 => "refuse" ]
    $context->hasProperty($document, 'visible'); // true
    $context->getFactory(); // Return an instance of FiniteFactory, used to instantiate the state machine
    $context->getStateMachine($document); // Returns a initialized StateMachine instance for $document


    // Throw a 404 if document isn't visible
    if (!$this->get('finite.context')->getProperties($document)['visible']) {
        throw $this->createNotFoundException(
            sprintf('The document "%s" is not in a visible state.', $document->getName())
        );
    }


Twig usage
----------

Although the Twig Extension is not Symfony-specific at all, when using the Symfony Bundle, Finite functions are
automatically accessible in your templates.

.. code-block:: jinja

    {{ dump(finite_state(document)) }} {# "draft" #}
    {{ dump(finite_transitions(document)) }} {# array:2 [ 0 => "propose", 1 => "refuse" ] #}
    {{ dump(finite_properties(document)) }} {# array:1 [ 'visible' => false ] #}
    {{ dump(finite_has(document, 'visible')) }} {# true #}
    {{ dump(finite_can(document, 'accept')) }} {# true #}


    {# Display reachable transitions #}
    {% for transition in finite_transitions(document) %}
        <a href="{{ path('document_apply_transition', {transition: transition}) }}">
            {{ transition }}
        </a>
    {% endfor %}


    {# Display an action if available #}
    {% if finite_can(document, 'accept') %}
        <button type="submit" name="accept">
            Accept this document
        </button>
    {% endif %}

Example
^^^^^^^

Using callbacks
---------------

The state machine is built around a a very flexible and powerful events / callbacks system.
Events dispatched with the EventDispatcher and works as the Symfony kernel events.

Events
^^^^^^

finite.set_initial_state:
    This event is fired when initializing a state machine with an object which does not have a defined state.
    It allows you to manage the default initial state of your object.

finite.initialize:
    Fired when the StateMachine is initialized for an object (event if the current object state is known)

finite.test_transition:
    Fired when testing if a transition can be applied, when you call ``StateMachine#can`` or ``StateMachine#apply``.
    This event is an instance of ``Finite\Event\TransitionEvent`` and can be rejected, which leads to a
    non-appliable transition. This is one of the most useful event, as it allows you to introduce business code
    for allowing / rejecting transitions

finite.test_transition.[transition_name]:
    Same as ``finite.test_transition`` but with the concerned transition in the event name.

finite.test_transition.[graph].[transition_name]:
    Same as ``finite.test_transition`` but with the concerned graph and transition in the event name.

finite.pre_transition:
    Fired before applying a transition. You can use it to prepare your object for a transition.

finite.pre_transition.[transition_name]:
    Same as ``finite.pre_transition`` but with the concerned transition in the event name.

finite.pre_transition.[graph].[transition_name]:
    Same as ``finite.pre_transition`` but with the concerned graph and transition in the event name.

finite.post_transition:
    Fired after applying a transition. You can use it to execute the business code you have to execute when
    a transition is applied.

finite.post_transition.[post_transition]:
    Same as ``finite.post_transition`` but with the concerned transition in the event name.

finite.post_transition.[graph].[transition_name]:
    Same as ``finite.post_transition`` but with the concerned graph and transition in the event name.


Callbacks
^^^^^^^^^

Callbacks are a simplified mechanism allowing you to plug your domain services on the finite events.
You can see it as a way to listen to events without defining a listener class that just redirects the events to
your services.

Using YAML configuration
........................

.. code-block:: yaml

    finite_finite:

        document_workflow:
            class: MyDocument
            states:
                # ...
            transitions:
                # ...

            callbacks:
                before:
                    # Will call the `sendPublicationMail` method of `@app.mailer.document` service
                    # When the `accept` transition is applied
                    send_publication_mail:
                        disabled: false # default value
                        on: accept
                        do: [ @app.mailer.document, 'sendPublicationMail' ]

                    # Will call the `sendNotAnymoreProposedEmail` method of `@app.mailer.document` service
                    # When any transition from the `proposed` state is applied.
                    # This condition can be negated by prefixing a `-` before the state name
                    # And the same exists for the destination transitions (with `to: `)
                    send_publication_mail:
                        disabled: false # default value
                        from: ['proposed']
                        do: [ @app.mailer.document, 'sendNotAnymoreProposedEmail' ]

Configuration reference
-----------------------

.. code-block:: yaml

    finite_finite:

        # Prototype
        name: # internal name of your graph, not used
            class:                ~           # Required, FQCN of your class
            graph:                default     # Name of your graph, keep default if using a single graph
            property_path:        finiteState # The property of your class used to store the state


            states:
                # Prototype
                name:            # Required, Name of your state
                    type: normal # State type, in "initial", "normal", "final"
                    properties:  # Properties array.
                        # Prototype
                        name:                 ~


            transitions:
                # Prototype
                name:           # Required, Name of your transition
                    from: []    # Required, states the transition can come from
                    to:   ~     # Required, state where the transition go
                    properties: # Properties array.
                        # Prototype
                        name:                 ~

            callbacks:

                before: # Pre-transition callbacks
                    # Prototype
                    name:
                        do:       ~ # Required. The callback.
                        on:       ~ # On which transition to trigger the callback. Default null
                        from:     ~ # From which states are we triggering the callback. Default null
                        to:       ~ # To which states are we triggering the callback. Default null
                        disabled: false

                after: # Post-transition callbacks
                    # Prototype
                    name:
                        on:                   ~
                        do:                   ~
                        from:                 ~
                        to:                   ~
                        disabled:             false
