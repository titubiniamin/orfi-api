<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('val_id')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->string('store_id')->nullable();
            $table->decimal('amount')->default(0);
            $table->decimal('store_amount')->nullable();
            $table->enum('status',['success','processing','failed','completed','canceled','pending']);
            $table->string('currency')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_no')->nullable();
            $table->string('card_issuer')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('card_sub_brand')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
