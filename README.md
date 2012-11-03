Finite, A Simple PHP Finite State Machine
=========================================

Finite is a Simple State Machine, written in PHP. It can manage any Stateful object by defining states and transitions between these states.

Features
--------

* Managing State/Transition graph for an object
* Defining and retrieving properties for states
* Event Listenable transitions
* Symfony2 integration
* Custom workflow loaders
* Extendable States and Transitions
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
Your stageful object just need to implement the `StatefulInterface` Interface.

```php
use Finite\StatefulInterface;

class Document implements StatefulInterface
{
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

// $document = retrieve your stageful object

$sm = new StateMachine();

// Define states
$sm->addState('s1');
$sm->addState('s2');
$sm->addState('s3');
$sm->addState('s4');

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

// Can we process a transitions ?
$sm->can('t34');

```
 
