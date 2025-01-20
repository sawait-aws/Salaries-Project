<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_box',
        'receiver_box',
        'amount',
        'sender_box_amount',
        'receiver_box_amount',
        'user_id',
        'user_first_name',
        'user_last_name',
        'commission_kind',  // Kind of commission
        'commission_amount',  // Amount of commission
        'transaction_date',
    ];
}
