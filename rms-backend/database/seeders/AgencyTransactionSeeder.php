<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgencyTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('agency_transactions')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('agency_transactions')->insert([
            ['discom' => 'MVVNL', 'account_no' => 'AC1001', 'txn_id' => 'TXN001', 'txn_date' => '2026-06-24', 'txn_time' => '10:00:00', 'amount' => 100],
            ['discom' => 'MVVNL', 'account_no' => 'AC1002', 'txn_id' => 'TXN002', 'txn_date' => '2026-06-24', 'txn_time' => '10:05:00', 'amount' => 200],
            ['discom' => 'MVVNL', 'account_no' => 'AC1003', 'txn_id' => 'TXN003', 'txn_date' => '2026-06-24', 'txn_time' => '10:10:00', 'amount' => 300],
            ['discom' => 'MVVNL', 'account_no' => 'AC1004', 'txn_id' => 'TXN004', 'txn_date' => '2026-06-24', 'txn_time' => '10:15:00', 'amount' => 400],
            ['discom' => 'MVVNL', 'account_no' => 'AC1005', 'txn_id' => 'TXN005', 'txn_date' => '2026-06-24', 'txn_time' => '10:20:00', 'amount' => 500],
            ['discom' => 'MVVNL', 'account_no' => 'AC1006', 'txn_id' => 'TXN006', 'txn_date' => '2026-06-24', 'txn_time' => '10:25:00', 'amount' => 600],
            ['discom' => 'MVVNL', 'account_no' => 'AC1007', 'txn_id' => 'TXN007', 'txn_date' => '2026-06-24', 'txn_time' => '10:30:00', 'amount' => 700],
            ['discom' => 'MVVNL', 'account_no' => 'AC1008', 'txn_id' => 'TXN008', 'txn_date' => '2026-06-24', 'txn_time' => '10:35:00', 'amount' => 800],
            ['discom' => 'MVVNL', 'account_no' => 'AC1009', 'txn_id' => 'TXN009', 'txn_date' => '2026-06-24', 'txn_time' => '10:40:00', 'amount' => 900],
            ['discom' => 'MVVNL', 'account_no' => 'AC1010', 'txn_id' => 'TXN010', 'txn_date' => '2026-06-24', 'txn_time' => '10:45:00', 'amount' => 1000],
            ['discom' => 'MVVNL', 'account_no' => 'AC1011', 'txn_id' => 'TXN011', 'txn_date' => '2026-06-24', 'txn_time' => '10:50:00', 'amount' => 1100],
            ['discom' => 'MVVNL', 'account_no' => 'AC1012', 'txn_id' => 'TXN012', 'txn_date' => '2026-06-24', 'txn_time' => '10:55:00', 'amount' => 1200],
            ['discom' => 'MVVNL', 'account_no' => 'AC1013', 'txn_id' => 'TXN013', 'txn_date' => '2026-06-24', 'txn_time' => '11:00:00', 'amount' => 1300],
            ['discom' => 'MVVNL', 'account_no' => 'AC1014', 'txn_id' => 'TXN014', 'txn_date' => '2026-06-24', 'txn_time' => '11:05:00', 'amount' => 1400],
            ['discom' => 'MVVNL', 'account_no' => 'AC1015', 'txn_id' => 'TXN015', 'txn_date' => '2026-06-24', 'txn_time' => '11:10:00', 'amount' => 1500],
            ['discom' => 'MVVNL', 'account_no' => 'AC1016', 'txn_id' => 'TXN016', 'txn_date' => '2026-06-24', 'txn_time' => '11:15:00', 'amount' => 500],
            ['discom' => 'MVVNL', 'account_no' => 'AC1017', 'txn_id' => 'TXN017', 'txn_date' => '2026-06-24', 'txn_time' => '11:20:00', 'amount' => 700],
            ['discom' => 'MVVNL', 'account_no' => 'AC1018', 'txn_id' => 'TXN018', 'txn_date' => '2026-06-24', 'txn_time' => '11:25:00', 'amount' => 1000],
            ['discom' => 'MVVNL', 'account_no' => 'AC1019', 'txn_id' => 'TXN019', 'txn_date' => '2026-06-24', 'txn_time' => '11:30:00', 'amount' => 600],
            ['discom' => 'MVVNL', 'account_no' => 'AC1020', 'txn_id' => 'TXN020', 'txn_date' => '2026-06-24', 'txn_time' => '11:35:00', 'amount' => 800],
        ]);
    }
}
