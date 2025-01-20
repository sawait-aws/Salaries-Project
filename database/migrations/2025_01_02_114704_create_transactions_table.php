<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('sender_box');  // Name of the sender's box
            $table->string('receiver_box');  // Name of the receiver's box
            $table->decimal('amount', 15, 2);  // Amount being transferred
            $table->decimal('sender_box_amount', 15, 2);  // Amount in the sender's box before transaction
            $table->decimal('receiver_box_amount', 15, 2);  // Amount in the receiver's box before transaction
            $table->string('user_id');  // User who made the transaction
            $table->string('user_first_name');  // First name of the user
            $table->string('user_last_name');  // Last name of the user
            $table->string('commission_kind'); // Kind of commission: percentage or static
            $table->decimal('commission_amount', 15, 2); // Amount of commission applied
            $table->timestamp('transaction_date');  // Date of the transaction
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
