<?php

namespace App\Command;

use App\Worker\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StartWorkerCommand
 * @package App\Command
 */
class StartWorkerCommand extends Command
{
    protected static $defaultName = 'app:start-worker';

    /** @var Worker  */
    protected $worker;

    public function __construct(Worker $worker)
    {
        $this->worker = $worker;
        $this->setSignalHandlers();
        parent::__construct();
    }

    protected function setSignalHandlers()
    {
        pcntl_async_signals(true);
        pcntl_signal(SIGTERM, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGINT, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGQUIT, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGHUP, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGUSR1, [$this, 'terminalSignalHandler']);
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts the Distributed Worker Instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Worker starting...");
        $this->worker->start();
        return Command::SUCCESS;
    }

    protected function terminalSignalHandler(OutputInterface $output): void
    {
        $output->writeln("Worker stopping...");
        exit(0);
    }
}