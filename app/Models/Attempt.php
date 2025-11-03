<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = [
        'user_id',
        'passage_id',
        'spoken_text',
        'accuracy_score',
        'feedback',
        'student_name'
    ];
}
