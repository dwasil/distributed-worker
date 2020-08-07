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
    private $caller;

    /** @var JobRepository */
    private $jobRepository;

    /** @var int ms */
    private const SLEEP_TIME_IF_NO_RECORDS = 100000;

    /** @var bool  */
    private $stopFlag = false;

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
        while (true) {

            if($this->stopFlag)
            {
                break;
            }

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

    /**
     * Stops worker
     */
    public function stop(): void
    {
        $this->stopFlag = true;
    }
}