<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Command;

use Finite\Tests\Extension\Symfony\Fixtures\State\DocumentState;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DumpStateMachineCommandTest extends KernelTestCase
{
    private ?CommandTester $commandTester = null;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('finite:state-machine:dump');
        $this->commandTester = new CommandTester($command);
    }

    public function testItReturnsMermaidDump(): void
    {
        $this->commandTester->execute([
            'state_enum' => DocumentState::class,
            'format' => 'mermaid',
        ]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testItFailsWithUnknownStateEnum(): void
    {
        $this->commandTester->execute([
            'state_enum' => 'UnknownStateEnum',
            'format' => 'mermaid',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    public function testItFailsWithUnknownFormat(): void
    {
        $this->commandTester->execute([
            'state_enum' => DocumentState::class,
            'format' => 'blobfish',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
    }

    protected static function getKernelClass(): string
    {
        return \AppKernel::class;
    }
}
