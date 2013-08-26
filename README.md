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

