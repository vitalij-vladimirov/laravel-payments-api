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
                'code'      => TransactionService::ERROR_BAD_INPUT,
                'message'   => 'Bad input data',
            ],[
                'code'      => TransactionService::ERROR_PROVIDER_NOT_FOUND,
                'message'   => 'Internal error: provider no found or disabled',
            ],[
                'code'      => TransactionService::ERROR_TOTAL_LIMIT,
                'message'   => 'Total user limit 1000 of current amount is reached',
            ],[
                'code'      => TransactionService::ERROR_HOUR_LIMIT,
                'message'   => 'Transactions limit is 10 per hour',
            ],[
                'code'      => TransactionService::ERROR_BAD_AUTHENTICATION,
                'message'   => 'Authentication code is wrong',
            ],[
                'code'      => TransactionService::ERROR_NOT_FOUND,
                'message'   => 'Transaction not found',
            ]
        ]);
    }
}
