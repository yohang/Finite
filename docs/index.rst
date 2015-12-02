Finite
======

.. toctree::
    :numbered:
    :maxdepth: 1

    usage/symfony
    examples/basic_graph
    examples/callbacks
    examples/transition-properties

A PHP Finite State Machine
--------------------------

Finite is a state machine library that gives you ability to manage the state of a PHP object through
a graph of states and transitions.


Overview
--------

Define your workflow / state graph
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    $document     = new MyDocument;
    $stateMachine = new Finite\StateMachine\StateMachine;
    $loader       = new Finite\Loader\ArrayLoader([
        'class'  => 'MyDocument',
        'states' => [
            'draft'    => ['type' => 'initial', 'properties' => []],
            'proposed' => ['type' => 'normal',  'properties' => []],
            'accepted' => ['type' => 'final',   'properties' => []],
            'refused'  => ['type' => 'final',   'properties' => []],
        ],
        'transitions' => [
            'propose' => ['from' => ['draft'],    'to' => 'proposed'],
            'accept'  => ['from' => ['proposed'], 'to' => 'accepted'],
            'refuse'  => ['from' => ['proposed'], 'to' => 'refused'],
        ]
    ]);

    $loader->load($stateMachine);
    $stateMachine->setObject($document);
    $stateMachine->initialize();


.. _index_define_your_object:

Define your object
^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    class MyDocument implements Finite\StatefulInterface
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



Work with states & transitions
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    <?php

    echo $stateMachine->getCurrentState();
    // => "draft"

    var_dump($stateMachine->can('accept'));
    // => bool(false)

    var_dump($stateMachine->can('propose'));
    // => bool(true)

    $stateMachine->apply('propose');
    echo $stateMachine->getCurrentState();
    // => "proposed"


Contribute
----------

Contributions are welcome !

Finite follows PSR-2 code, and accept pull-requests on the `GitHub repository <https://github.com/yohang/Finite>`_.

If you're a beginner, you will find some guidelines about code contributions at `Symfony <http://symfony.com/doc/current/contributing/code/patches.html>`_.

.. raw:: html

    <div id="share-buttons">
        <a href="https://twitter.com/rouKs" class="twitter-follow-button" data-show-count="false" data-lang="en" data-size="large">Follow @rouKs</a>
        <a href="https://twitter.com/share" class="twitter-share-button" data-text="#Finite A PHP5.3+ Finite State Machine" data-via="rouKs" data-lang="fr" data-size="large" data-related="rouKs">Tweeter</a>
    </div>
    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
