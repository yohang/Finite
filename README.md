Finite, A Simple PHP Finite State Machine
=========================================

Finite is a Simple State Machine, written in PHP. 
It can manage any Stateful object by defining states and transitions between these states.

As of version 2, Finite is a low-deps and lightweight state machine library, thanks to the use of PHP Enums.


![CI Status](https://github.com/yohang/finite/actions/workflows/ci.yml/badge.svg)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyohang%2FFinite%2Fmain)](https://dashboard.stryker-mutator.io/reports/github.com/yohang/Finite/main)

Disclaimer
----------

I don't have the time anymore to maintain this lib. Here is the documentation for the brand new Finite V2, based
on PHP >= 8.1 Enums, but don't expect it to be updated on a regular basis.

Features
--------

* Manage State/Transition graph for an object
* Attach business logic to states
* Listen to transitions between state to trigger your domain code
* Symfony integration
* Twig Extension

Getting started
---------------

### Installation (via composer)
```bash
 $ composer req yohang/finite
```

### Define your state enum

```php
enum DocumentState: string implements State
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REPORTED = 'reported';
    case DISABLED = 'disabled';

    public static function getTransitions(): array
    {
        return [
            new Transition('publish', [self::DRAFT], self::PUBLISHED),
            new Transition('clear', [self::REPORTED, self::DISABLED], self::PUBLISHED),
            new Transition('report', [self::PUBLISHED], self::REPORTED),
            new Transition('disable', [self::REPORTED, self::PUBLISHED], self::DISABLED),
        ];
    }
}

```


### Define your Stateful Object

Your stateful object just need to have a state property


```php
class Document
{
    private DocumentState $state = DocumentState::DRAFT;

    public function getState(): DocumentState
    {
        return $this->state;
    }

    public function setState(DocumentState $state): void
    {
        $this->state = $state;
    }
}
```


### Initializing a simple StateMachine

```php
use Finite\StateMachine;

$document = new Document;

$sm = new StateMachine;

// Can we process a transition ?
$sm->can($document, 'publish');

// Apply a transition
$sm->apply($document, 'publish'); 

```

### Add business logic to states

Finite < 2.0 had properties on states. A metadata mechanism that allowed to add business properties on states to 
define the behavior of on object, depending on its state.

The idea behind this was to avoid to test the state in your domain code (A controller must not throw a 404 if the state 
is draft. But it can throw a 404 if the current state does not have a "visible" property. That was the idea).

Finite 2 does not needs this. PHP Enums can have methods. So, replace your properties with simple methods on your state.

```php
enum DocumentState: string implements State
{
    // ...

    public function isDeletable(): bool
    {
        return in_array($this, [self::DRAFT, self::DISABLED]);
    }

    public function isPrintable(): bool
    {
        return in_array($this, [self::PUBLISHED, self::REPORTED]);
    }
}
```

After that, you can use theses methods on your object, even without instantiating the state machine.

```php
var_dump($document->getState()->isDeletable());
var_dump($document->getState()->isPrintable());
```
