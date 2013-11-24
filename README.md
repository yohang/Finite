Finite, A Simple PHP Finite State Machine
=========================================

Finite is a Simple State Machine, written in PHP. It can manage any Stateful object by defining states and transitions between these states.

Features
--------

* Managing State/Transition graph for an object
* Defining and retrieving properties for states
* Event Listenable transitions
* Symfony2 integration
* Custom state graph loaders
* Twig Extension

Getting started
---------------

### Installation (via composer)
```js
{
      "require": {
        "yohang/finite": "1.0.*"
    }
}
```

### Define your Stateful Object
Your stateful object just need to implement the `StatefulInterface` Interface.

```php
use Finite\StatefulInterface;

class Document implements StatefulInterface
{
        private $state;
        public function setFiniteState($state)
        {
                $this->state = $state;
        }

        public function getFiniteState()
        {
            return $this->state;
        }
}
```

### Initializing a simple StateMachine

```php
use Finite\StateMachine\StateMachine;
use Finite\State\State;
use Finite\State\StateInterface;

// $document = retrieve your stateful object

$sm = new StateMachine();

// Define states
$sm->addState(new State('s1', StateInterface::TYPE_INITIAL));
$sm->addState('s2');
$sm->addState('s3');
$sm->addState(new State('s4', StateInterface::TYPE_FINAL));

// Define transitions
$sm->addTransition('t12', 's1', 's2');
$sm->addTransition('t23', 's2', 's3');
$sm->addTransition('t34', 's3', 's4');
$sm->addTransition('t42', 's4', 's2');

// Initialize
$sm->setObject($document);
$sm->initialize();

// Retrieve current state
$sm->getCurrentState();

// Can we process a transition ?
$sm->can('t34');

```


### Rendering State Machines as Graphs

You can render a state machine using Graphviz (see examples/rendered-graph.png).

To obtain the source code of a directed graph in dot simply initialize the graphviz renderer:

```php
use Finite\StateMachine\StateMachine;
use Finite\Visualisation\Graphviz;
use Finite\State\StateInterface;

$stateMachine = new StateMachine();
$renderer     = new Graphviz();
$dotSource    = $renderer->render($stateMachine);

```

To change how nodes are rendered, pass a configuration. The configuration accepts two parameters which control the generated output:

* An optional flag controlling whether the state properties shall be rendered within the label.
* The optional fill color (a dot color name or e.g. hex code) of init and end states.

```php
use Finite\StateMachine\StateMachine;
use Finite\Visualisation\Configuration;
use Finite\Visualisation\Graphviz;
use Finite\State\StateInterface;

$stateMachine = new StateMachine();
$config       = new Configuration(true, 'red');
$renderer     = new Graphviz($config);
$dotSource    = $renderer->render($stateMachine);

```


