<?php

namespace App\Services\Reconciliation;

use Generator;

class ChunkManagerService
{
    public function chunk(iterable $rows, int $chunkSize = 5000): Generator
    {
        $chunk = [];

        foreach ($rows as $row) {
            $chunk[] = $row;

            if (count($chunk) >= $chunkSize) {
                yield $chunk;
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            yield $chunk;
        }
    }
}