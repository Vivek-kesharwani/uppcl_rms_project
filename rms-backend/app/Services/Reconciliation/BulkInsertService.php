<?php

namespace App\Services\Reconciliation;

use App\Models\StagingTransaction;

class BulkInsertService
{
    /**
     * Bulk insert one cleaned & validated chunk.
     */
    public function insert(array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        StagingTransaction::insert($rows);
    }
}