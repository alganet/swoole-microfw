<?php declare(strict_types=1);

namespace DvdSales\Routes;
use DvdSales\Models\Actors;
use Swoole\Http\Request;
use Swoole\Http\Response;

class GetActors
{
    public function __construct(private Actors $actorsModel) {}

    public function handle(Request $request, Response $response)
    {
        $cursor = intval($request->get['cursor'] ?? 0);

        $response->header('Content-Type', 'application/json');
        $response->write(
            json_encode([
                'actors' => iterator_to_array(
                    $this->actorsModel->iterateMany($cursor),
                    preserve_keys: true
                ),
                'cursor' => $cursor
            ])
        );
    }
}

