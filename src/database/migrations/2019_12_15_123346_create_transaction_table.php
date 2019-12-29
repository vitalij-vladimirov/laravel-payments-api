<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\TransactionService;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')
                ->index('user_id');
            $table->string('details', 255);
            $table->string('receiver_account', 255);
            $table->string('receiver_name', 255);
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2);
            $table->string('currency', 3);
            $table->integer('provider_id')
                ->index('provider_id')
                ->nullable();
            $table->string('provider_trn_id')
                ->nullable()
                ->comment('Provider responded transaction ID.');
            $table->enum('status', [
                    TransactionService::STATUS_RECEIVED,
                    TransactionService::STATUS_CONFIRMED,
                    TransactionService::STATUS_SUBMITTED,
                    TransactionService::STATUS_COMPLETED,
                    TransactionService::STATUS_ERROR,
                ]);
            $table->string('error_code', 20)
                ->nullable()
                ->comment('In case of error only.');
            $table->timestamp('created_at')
                ->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')
                ->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
