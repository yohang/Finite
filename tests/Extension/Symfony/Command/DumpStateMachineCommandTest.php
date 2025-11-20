<?php

declare(strict_types=1);

namespace Finite\Tests\Extension\Symfony\Command;

use Finite\Tests\Extension\Symfony\Fixtures\State\DocumentState;
use Finite\Tests\Extension\Symfony\Fixtures\State\NonStateEnum;
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
        $this->assertStringContainsString(
            <<<MERMAID
            title: Finite\Tests\Extension\Symfony\Fixtures\State\DocumentState
            ---
            stateDiagram-v2
                draft --> published: publish
                reported --> published: clear
                disabled --> published: clear
                published --> reported: report
                reported --> disabled: disable
                published --> disabled: disable
            MERMAID,
            $this->commandTester->getDisplay(),
        );
    }

    public function testItFailsWithUnknownStateEnum(): void
    {
        $this->commandTester->execute([
            'state_enum' => 'UnknownStateEnum',
            'format' => 'mermaid',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('[ERROR] The state enum "UnknownStateEnum" does not exist.', $this->commandTester->getDisplay());
    }

    public function testItFailsWithNonStateEnum(): void
    {
        $this->commandTester->execute([
            'state_enum' => NonStateEnum::class,
            'format' => 'mermaid',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString(
            '[ERROR] The state enum',
            $this->commandTester->getDisplay(),
        );
        $this->assertStringContainsString(
            '"Finite\Tests\Extension\Symfony\Fixtures\State\NonStateEnum"',
            $this->commandTester->getDisplay(),
        );
    }

    public function testItFailsWithUnknownFormat(): void
    {
        $this->commandTester->execute([
            'state_enum' => DocumentState::class,
            'format' => 'blobfish',
        ]);

        $this->assertSame(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('[ERROR] Unknown format "blobfish". Supported formats are: mermaid', $this->commandTester->getDisplay());
    }

    protected static function getKernelClass(): string
    {
        return \AppKernel::class;
    }
}
