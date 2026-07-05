<?php

namespace App\Services\Reconciliation;

use App\Models\Source;
use InvalidArgumentException;

class SourceResolverService
{
    public function resolve(string $sourceName): Source
    {
        $source = Source::where('source_name', $sourceName)
            ->where('is_active', true)
            ->first();

        if (!$source) {
            throw new InvalidArgumentException(
                "Source '{$sourceName}' is not registered or inactive."
            );
        }

        return $source;
    }
}