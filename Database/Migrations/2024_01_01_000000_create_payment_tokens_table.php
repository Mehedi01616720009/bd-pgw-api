<?php

use Core\Database\Migration;
use Core\Database\Schema;
use Core\Database\Blueprint;

class CreatePaymentTokensTable extends Migration
{
    public function up(): void
    {
        Schema::create('PaymentTokens', function (Blueprint $table) {
            $table->id();
            $table->text('token');
            $table->text('refreshToken');
            $table->dateTime('expiredAt');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Users');
    }
}
