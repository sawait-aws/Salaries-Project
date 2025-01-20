<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysOffRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'position',
        'first_name',
        'last_name',
        'date',
        'day_off_kind',
        'emp_notes',
        'manager_notes',
        'top_manager_notes',
        'status',
        'proof',
    ];
}
