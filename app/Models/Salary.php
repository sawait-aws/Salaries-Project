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
        'working_days',
        'unpaid_days',
        'sick_leave',
        'remaining_annual_days_off',
        'deduction',
        'bonus',
        'salary_to_be_paid',
    ];

    // Relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
