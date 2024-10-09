<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'year',
        'month',
        'gross_salary',
        'commission',
        'salaf',
        'salaf_deducted',
        'salary_to_be_paid',
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
