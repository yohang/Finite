<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Fixtures\Controller;

use Finite\Tests\Extension\Symfony\Fixtures\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class FiniteController
{
    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function __invoke(): Response
    {
        $document = new Document();

        return new Response(
            $this->twig->render(
                'finite.html.twig',
                [
                    'document' => $document,
                ]
            ),
        );
    }
}
