<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type');
            $table->string('description');
            $table->decimal('amount')->default(0);
            $table->decimal('discount_amount')->default(0);
            $table->string('color');
            $table->string('active_color')->default('#f2fafd');
            $table->boolean('is_active')->default(1)->comment('1 = active, 0 = deactive');
            $table->integer('duration')->default(1);
            $table->enum('duration_type',['hour','day','week','month','year']);
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
        Schema::dropIfExists('subscription_plans');
    }
}
