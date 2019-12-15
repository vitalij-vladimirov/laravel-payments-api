<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Services\ProviderService;

class CreateProviderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provider', function (Blueprint $table) {
            $table->increments('id');
            $table->string('provider_key', 20)
                ->unique();
            $table->string('title', 50);
            $table->enum('status', [
                    ProviderService::STATUS_ACTIVE,
                    ProviderService::STATUS_DISABLED,
                ])
                ->default(ProviderService::STATUS_ACTIVE);
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
        Schema::dropIfExists('provider');
    }
}
