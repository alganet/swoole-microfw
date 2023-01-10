<?php declare(strict_types=1);

use DvdSales\Models\Actors;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\Constraint\IsAnything;
use Doctrine\DBAL\Result;
use DvdSales\MessageTypes\Actor;

class ActorsTest extends \PHPUnit\Framework\TestCase
{
    /** @covers \DvdSales\Models\Actors::iterateMany */
    public function testCanListActors()
    {
        // 1. Setup Model with Doctrine Mocks
        $mockGenerator = (fn () => yield from [
            [
                'actor_id' => 123,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'last_update' => '2006-02-15 04:34:33'
            ],
            [
                'actor_id' => 123,
                'first_name' => 'Jane',
                'last_name' => 'Dough',
                'last_update' => '2006-02-15 04:34:33'
            ]
        ])();
        $mockResult = $this->createMock(Result::class);
        $mockResult
            ->expects($this->any())
            ->method('iterateAssociative')
            ->will($this->returnValue($mockGenerator));
        $mockQueryBuilder = $this->createMock(QueryBuilder::class);
        $mockQueryBuilder
            ->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($mockResult));
        $mockQueryBuilder
            ->expects($this->any())
            ->method(new IsAnything())
            ->will($this->returnSelf());

        $actorsModel = new Actors($mockQueryBuilder);

        // 2. Call the model
        $result = $actorsModel->iterateMany(idCursor: 0);
        
        // 3. Assert result
        $this->assertInstanceOf(Traversable::class, $result);
        $r = $result->current();
        $this->assertInstanceOf(Actor::class, $r);
        $this->assertEquals(123, $r->actorId);
        $this->assertEquals('John', $r->firstName);
        $this->assertEquals('Doe', $r->lastName);
        $this->assertInstanceOf(\DateTimeImmutable::class, $r->lastUpdate);
        $this->assertEquals('2006-02-15T04:34:33+00:00', $r->lastUpdate->format(DateTimeInterface::W3C));
    }
}