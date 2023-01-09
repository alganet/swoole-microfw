<?php

namespace DvdSales\Routes;

use Doctrine\DBAL\Connection;

class GetActors
{
    public function __construct(
        private Connection $connection
    ) {}

    public function handle()
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from('sakila.actor');

        $result = $qb->executeQuery();

        foreach ($result->iterateAssociative() as $record) {
            echo $record['first_name'] . PHP_EOL;
        }
    }
}

