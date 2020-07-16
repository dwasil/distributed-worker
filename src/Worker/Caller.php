<?php

namespace App\Worker;

use App\Entity\Job;
use http\Exception\RuntimeException;

/**
 * Class Caller
 * @package App\Worker
 */
class Caller
{
    /** @var resource */
    protected $cUrl;

    public function __construct()
    {
        if (!($this->cUrl = curl_init())) {
            throw new RuntimeException ("Can\'t init cURL");
        }

        curl_setopt($this->cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->cUrl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->cUrl, CURLOPT_MAXREDIRS, 5);
    }

    public function __destruct()
    {
        curl_close($this->cUrl);
    }

    /**
     * Call url from Job via http
     * @param Job $job
     * @return Job
     */
    public function callJobUrl(Job $job): Job
    {
        curl_setopt($this->cUrl, CURLOPT_URL, $job->getUrl());

        if (curl_exec($this->cUrl)) {
            $status = Job::STATUS_DONE;
        } else {
            /*
             * todo: may be we need to mark as error responses with response code == 5xx ?
             */
            $status = Job::STATUS_ERROR;
        }

        $httpCode = curl_getinfo($this->cUrl, CURLINFO_RESPONSE_CODE);
        $job->setStatus($status);
        $job->setHttpCode($httpCode);

        return $job;
    }
}