<?php

namespace Finite\Extension\Symfony\Command;

use Finite\Dumper\MermaidDumper;
use Finite\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DumpStateMachineCommand extends Command
{
    private const FORMAT_MERMAID = 'mermaid';
    private const FORMATS = [self::FORMAT_MERMAID];

    protected function configure(): void
    {
        $this
            ->setName('finite:state-machine:dump')
            ->setDescription('Dump the state machine graph into requested format')
            ->addArgument('state_enum', InputArgument::REQUIRED, 'The state enum to use')
            ->addArgument('format', InputArgument::REQUIRED, 'The format to dump the graph in');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $stateEnum = (string)$input->getArgument('state_enum');

        if (!(enum_exists($stateEnum) && is_subclass_of($stateEnum, State::class))) {
            $io->error('The state enum "' . $stateEnum . '" does not exist.');

            return self::FAILURE;
        }

        $format = (string)$input->getArgument('format');
        switch ($format) {
            case self::FORMAT_MERMAID:
                /** @psalm-suppress ArgumentTypeCoercion Type is enforced upper but not detected by psalm */
                $output->writeln((new MermaidDumper)->dump($stateEnum));

                break;
            default:
                $output->writeln('Unknown format "' . $format . '". Supported formats are: ' . implode(', ', self::FORMATS));

                return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
