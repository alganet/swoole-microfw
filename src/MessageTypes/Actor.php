<?php declare(strict_types=1);

namespace DvdSales\MessageTypes;

class Actor
{
    public function __construct(
        readonly public int $actorId,
        readonly public string $firstName,
        readonly public string $lastName,
        readonly public ?\DateTimeImmutable $lastUpdate,
    ) {}
}
