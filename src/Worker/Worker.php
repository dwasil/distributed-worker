<?php

namespace App\Worker;

use App\Repository\JobRepository;

/**
 * Class Worker
 * @package App\Worker
 */
class Worker
{
    /** @var Caller */
    protected $caller;

    /** @var JobRepository */
    private $jobRepository;

    /** @var int ms */
    protected const SLEEP_TIME_IF_NO_RECORDS = 100000;

    /**
     * Worker constructor.
     * @param JobRepository $jobRepository
     * @param Caller $caller
     */
    public function __construct(JobRepository $jobRepository, Caller $caller)
    {
        $this->jobRepository = $jobRepository;
        $this->caller = $caller;
    }

    /**
     * Main workflow method
     */
    public function start(): void
    {
		pcntl_async_signals(TRUE);
		pcntl_signal(SIGTERM, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGINT, [$this, 'terminalSignalHandler']);
        pcntl_signal(SIGQUIT, [$this, 'terminalSignalHandler']);

        while (true) {
            $job = $this->jobRepository->getNextJob();

            if ($job) {
                $job = $this->caller->callJobUrl($job);
                $this->jobRepository->saveJob($job);
            } else {
                /*
                 * If there are no new records just sleep 100 ms before the next looping.
                 */
                usleep(static::SLEEP_TIME_IF_NO_RECORDS);
            }
        }
    }

    protected function terminalSignalHandler(int $signal): void
    {
        exit(0);
	}
}