Finite, A Simple PHP Finite State Machine
=========================================

Finite is a Simple State Machine, written in PHP. It can manage any Stateful object by defining states and transitions between these states.

[![Build Status](https://travis-ci.org/yohang/Finite.svg?branch=master)](https://travis-ci.org/yohang/Finite)
[![Latest Stable Version](https://poser.pugx.org/yohang/finite/v/stable.png)](https://packagist.org/packages/yohang/finite)
[![Total Downloads](https://poser.pugx.org/yohang/finite/downloads.png)](https://packagist.org/packages/yohang/finite)
[![License](https://poser.pugx.org/yohang/finite/license.png)](https://packagist.org/packages/yohang/finite)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yohang/Finite/badges/quality-score.png?s=d6b74d46e3e3f66431270ec39204d98764cb12cb)](https://scrutinizer-ci.com/g/yohang/Finite/)
[![Code Coverage](https://scrutinizer-ci.com/g/yohang/Finite/badges/coverage.png?s=e1399f90a2ea42f4973e8bd79056540ff8de0ce4)](https://scrutinizer-ci.com/g/yohang/Finite/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/394f3a8e-e6c5-4102-8979-d389db2d0293/mini.png)](https://insight.sensiolabs.com/projects/394f3a8e-e6c5-4102-8979-d389db2d0293)
[![Dependency Status](https://www.versioneye.com/php/yohang:finite/1.0.3/badge.svg)](https://www.versioneye.com/php/yohang:finite/1.0.3)
[![Reference Status](https://www.versioneye.com/php/yohang:finite/reference_badge.svg?style=flat)](https://www.versioneye.com/php/yohang:finite/references)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/yohang/Finite?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

Features
--------

* Managing State/Transition graph for an object
* Defining and retrieving properties for states
* Event Listenable transitions
* Symfony2 integration
* Custom state graph loaders
* Twig Extension

Documentation
-------------

[Documentation for master (1.1)](http://finite.readthedocs.org/en/master/)

Getting started
---------------

### Installation (via composer)
```js
{
      "require": {
        "yohang/finite": "~1.1"
    }
}
```

### Version note :

If your are using this library in a Symfony project, 1.1 version is only compatible with Symfony `>=2.6`.
1.0 is compatible with Symfony `>=2.3, <2.6`.

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

