<?php

namespace App\Command;

use App\Repository\JobRepository;
use App\Worker\Caller;
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

    /** @var JobRepository  */
    private $jobRepository;

    public function __construct(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Starts the Distributed Worker Instance');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new Worker($this->jobRepository, new Caller());
        $output->writeln("Worker started");
        $worker->start();
        return Command::SUCCESS;
    }
}