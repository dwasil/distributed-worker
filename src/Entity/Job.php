<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JobRepository::class)
 */
class Job
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $http_code;

    public const STATUS_NEW = 'NEW';
    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_DONE = 'DONE';
    public const STATUS_ERROR = 'ERROR';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHttpCode(): ?int
    {
        return $this->http_code;
    }

    public function setHttpCode(?int $http_code): self
    {
        $this->http_code = $http_code;

        return $this;
    }
}
