<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillingTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('billing_transactions')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $records = [];

        for ($i = 1; $i <= 20; $i++) {
            $txnNo = str_pad($i, 3, '0', STR_PAD_LEFT);

            $amounts = [
                16 => 450,
                17 => 650,
                18 => 900,
                19 => 600,
                20 => 800,
            ];

            $amount = $amounts[$i] ?? ($i * 100);

            $time = date('H:i:s', strtotime('10:00:00 +' . (($i - 1) * 5) . ' minutes'));

            $records[] = [
                'discom' => 'MVVNL',
                'account_no' => 'AC10' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'txn_id' => 'TXN' . $txnNo,
                'txn_date' => '2026-06-24',
                'txn_time' => $time,
                'amount' => $amount,
                'agency_name' => 'BillDesk Agency',
            ];
        }

        \DB::table('billing_transactions')->insert($records);

    }
}
