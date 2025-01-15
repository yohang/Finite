<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Fixtures\Model;

use Finite\Tests\Extension\Symfony\Fixtures\State\DocumentState;

class Document
{
    public DocumentState $state = DocumentState::DRAFT;
}
