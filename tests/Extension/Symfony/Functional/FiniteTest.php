<?php
declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FiniteTest extends WebTestCase
{
    public function test_finite_controller(): void
    {
        $client = $this->createClient();
        $client->request('GET', '/finite');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.can', 'yes');
        $this->assertSelectorTextContains('.cannot', 'no');
        $this->assertSelectorTextContains('.reachables', 'publish');
    }

    protected static function getKernelClass(): string
    {
        return \AppKernel::class;
    }
}
