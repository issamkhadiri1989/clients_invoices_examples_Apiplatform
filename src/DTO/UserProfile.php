<?php

declare(strict_types=1);

namespace App\DTO;

class UserProfile
{
    public function __construct(private ?string $fullName, private ?\DateTime $joinedOn)
    {
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getJoinedOn(): \DateTime
    {
        return $this->joinedOn;
    }
}
