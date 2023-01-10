<?php declare(strict_types=1);

namespace DvdSales\Models;
use Doctrine\DBAL\Query\QueryBuilder;
use DvdSales\MessageTypes\Actor;

class Actors
{
    const CURSOR_SIZE = 100;
    public function __construct(
        private QueryBuilder $queryBuilder
    ) {}

    public function iterateMany(?int $idCursor = 0) : \Generator
    {
        $dataSource =  $this->queryBuilder
            ->select('actor_id', 'first_name', 'last_name', 'last_update')
            ->from('sakila.actor')
            ->setFirstResult($idCursor)
            ->setMaxResults(self::CURSOR_SIZE)
            ->executeQuery()
            ->iterateAssociative();

        foreach ($dataSource as $record) {
            yield new Actor(
                actorId: intval($record['actor_id']),
                firstName: $record['first_name'],
                lastName: $record['last_name'],
                lastUpdate: new \DateTimeImmutable($record['last_update'])
            );
        }
    }
}