<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSettlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('bank_settlements')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $records = [];

        for ($i = 1; $i <= 18; $i++) {
            $txnNo = str_pad($i, 3, '0', STR_PAD_LEFT);

            $amounts = [
                16 => 500,
                17 => 700,
                18 => 1000,
            ];

            $amount = $amounts[$i] ?? ($i * 100);

            $time = date('H:i:s', strtotime('12:00:00 +' . (($i - 1) * 5) . ' minutes'));

            $records[] = [
                'bank_ref_no' => 'BR' . $txnNo,
                'txn_id' => 'TXN' . $txnNo,
                'settlement_date' => '2026-06-24',
                'settlement_time' => $time,
                'settled_amount' => $amount,
                'settlement_status' => 'SUCCESS',
                'payment_gateway' => 'BillDesk',
            ];
        }

        \DB::table('bank_settlements')->insert($records);

    }
}
