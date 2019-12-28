<?php

use Illuminate\Database\Seeder;
use App\Services\TransactionService;

class ErrorCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('error_code')->insertOrIgnore([
            [
                'error_code'    => TransactionService::ERROR_BAD_INPUT,
                'error_message' => 'Bad input data',
            ],[
                'error_code'    => TransactionService::ERROR_PROVIDER_NOT_FOUND,
                'error_message' => 'Internal error: provider no found or disabled',
            ],[
                'error_code'    => TransactionService::ERROR_TOTAL_LIMIT,
                'error_message' => 'Total user limit 1000 of current amount is reached',
            ],[
                'error_code'    => TransactionService::ERROR_HOUR_LIMIT,
                'error_message' => 'Transactions limit is 10 per hour',
            ],[
                'error_code'    => TransactionService::ERROR_BAD_AUTHENTICATION,
                'error_message' => 'Authentication code is wrong',
            ],[
                'error_code'    => TransactionService::ERROR_NOT_FOUND,
                'error_message' => 'Transaction not found',
            ]
        ]);
    }
}
