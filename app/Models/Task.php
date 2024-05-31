<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'user_id',
        'priority',
        'due_date',
    ];

    // Define the relationship with Category
    public function category() {
        return $this->belongsTo(Category::class);
    }

    // Define the relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }
}
