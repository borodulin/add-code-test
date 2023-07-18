<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import', description: 'Import xml')]
class ImportCommand extends Command
{
    public function __construct(
        private readonly ImportService $importService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::REQUIRED, 'File Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $filename = $input->getArgument('filename');

        if (!file_exists($filename)) {
            throw new \RuntimeException('File is not found');
        }

        $this->importService->process(file_get_contents($filename));

        $io->success('OK');

        return self::SUCCESS;
    }
}
