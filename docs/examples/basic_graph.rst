Basic graph
===========

Goal
----

In this example, we'll see a basic Document workflow, following this graph : 

.. code-block:: text

                              Reject
                       |-----------------|
  Transitions          |                 |
                       v    Propose      |       Accept
  States            Draft ----------> Proposed ----------> Accepted

  Properties     * Deletable                              * Printable
                 * Editable



Implement the document class
----------------------------

.. code-block:: php

    <?php
    
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

Configure your graph
--------------------

.. code-block:: php

    <?php
    
    $loader = new Finite\Loader\ArrayLoader([
        'class'   => 'Document',
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


At this point, your Workflow / State graph is fully accessible to the state machine, and you can start to work with your workflow.

Working with workflow
---------------------

Current state
^^^^^^^^^^^^^

.. code-block:: php

    <?php
    // Get the name of the current state
    $stateMachine->getCurrentState()->getName();
    // string(5) "draft"
    
    // List the currently accessible properties, and their values
    $stateMachine->getCurrentState()->getProperties();
    // array(2) {
    //     'deletable' => bool(true)
    //     'editable' => bool(true)
    // }
    
    // Checks if "deletable" property is defined
    $stateMachine->getCurrentState()->has('deletable');
    // bool(true)
    
    // Checks if "printable" property is defined
    $stateMachine->getCurrentState()->has('printable');
    // bool(false)
    
Available transitions
^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php
    
    // Retrieve available transitions
    var_dump($stateMachine->getCurrentState()->getTransitions());
    // array(1) {
    //      [0] => string(7) "propose"
    // }
    
    // Check if we can apply the "propose" transition
    var_dump($stateMachine->getCurrentState()->can('propose'));
    // bool(true)
    
    // Check if we can apply the "accept" transition
    var_dump($stateMachine->getCurrentState()->can('accept'));
    // bool(false)
    
Apply transition
^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php
    
    // Trying to apply a not accessible transition
    try {
        $stateMachine->apply('accept');
    } catch (\Finite\Exception\StateException $e) {
        echo $e->getMessage();
    }
    // The "accept" transition can not be applied to the "draft" state.
    
    // Applying a transition
    $stateMachine->apply('propose');
    $stateMachine->getCurrentState()->getName();
    // string(7) "proposed"
    $document->getFiniteState();
    // string(7) "proposed"
