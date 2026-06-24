<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ReconciliationService;

class RunReconciliation extends Command
{
    protected $signature = 'rms:reconcile';

    protected $description = 'Run RMS reconciliation engine';

    public function handle(ReconciliationService $service): int
    {
        $summary = $service->run();

        $this->info('RMS Reconciliation Completed');
        $this->line('Total Transactions: ' . $summary['total']);
        $this->line('Matched: ' . $summary['matched']);
        $this->line('Exceptions: ' . $summary['exceptions']);
        $this->line('Amount Mismatch: ' . $summary['amount_mismatch']);
        $this->line('Missing Settlement: ' . $summary['missing_settlement']);

        return self::SUCCESS;
    }
}
