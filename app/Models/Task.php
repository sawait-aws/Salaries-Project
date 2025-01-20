<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',           // Name of the task
        'description',    // Optional description
        'status',         // Status of the task
        'priority',       // Priority of the task
        'emp',            // Array of employee names
        'review_details', // Optional review details
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'emp' => 'array', // Automatically cast the JSON 'emp' column to an array
    ];
}
