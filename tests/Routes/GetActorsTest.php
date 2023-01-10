<?php declare(strict_types=1);

use DvdSales\Models\Actors;
use DvdSales\MessageTypes\Actor;
use DvdSales\Routes\GetActors;

class GetActorsTest extends \PHPUnit\Framework\TestCase

/** @covers \DvdSales\Routes\GetActors::handle */{
    public function testCanListActors()
    {
        $stubDate = new \DateTimeImmutable('2006-02-15 04:34:33');
        $stubActor = new Actor(123, 'John', 'Doe', $stubDate);
        $expectedSerialization = json_encode([
            'actors' => [$stubActor],
            'cursor' => 0
        ]);
        $mockResult = (fn () => yield from [$stubActor])();
        $mockActorsModel = $this->createMock(Actors::class);
        $mockActorsModel
            ->expects($this->once())
            ->method('iterateMany')
            ->will($this->returnValue($mockResult));

        $mockRequest = $this->createMock(\Swoole\Http\Request::class);
        $mockResponse = $this->createMock(\Swoole\Http\Response::class);
        $mockResponse
            ->expects($this->once())
            ->method('header')
            ->with(
                $this->equalToIgnoringCase('Content-Type'),
                $this->equalTo('application/json')
            );

        $mockResponse
            ->expects($this->once())
            ->method('write')
            ->with($this->equalTo($expectedSerialization));

        $route = new GetActors($mockActorsModel);
        $route->handle($mockRequest, $mockResponse);
    }
}