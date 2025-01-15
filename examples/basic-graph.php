#!/usr/bin/env php
<?php

declare(strict_types=1);

use Finite\State;
use Finite\Transition\Transition;

require_once __DIR__.'/../vendor/autoload.php';

// Implement your State
enum DocumentState: string implements State
{
    public const PUBLISH = 'publish';
    public const CLEAR = 'clear';
    public const REPORT = 'report';
    public const DISABLE = 'disable';

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case REPORTED = 'reported';
    case DISABLED = 'disabled';

    public function isDeletable(): bool
    {
        return in_array($this, [self::DRAFT, self::DISABLED], true);
    }

    public function isPrintable(): bool
    {
        return in_array($this, [self::PUBLISHED, self::REPORTED], true);
    }

    public static function getTransitions(): array
    {
        return [
            new Transition(self::PUBLISH, [self::DRAFT], self::PUBLISHED),
            new Transition(self::CLEAR, [self::REPORTED, self::DISABLED], self::PUBLISHED),
            new Transition(self::REPORT, [self::PUBLISHED], self::REPORTED),
            new Transition(self::DISABLE, [self::REPORTED, self::PUBLISHED], self::DISABLED),
        ];
    }
}

// Implement your document class
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

// Configure your graph
$document = new Document();
$stateMachine = new Finite\StateMachine();

// Working with workflow

// Current state
var_dump($document->getState());

// Available transitions
var_dump($stateMachine->getReachablesTransitions($document));
var_dump($stateMachine->can($document, DocumentState::PUBLISH));
var_dump($stateMachine->can($document, DocumentState::PUBLISH, DocumentState::class));
var_dump($stateMachine->can($document, DocumentState::REPORT));
var_dump($stateMachine->can($document, DocumentState::REPORT, DocumentState::class));

// Apply transitions
try {
    $stateMachine->apply($document, DocumentState::REPORT);
} catch (Exception $e) {
    echo $e->getMessage(), "\n";
}

// Applying a transition
$stateMachine->apply($document, DocumentState::PUBLISH);
var_dump($document->getState());
