<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'topic_id',
        'description',
    ];

    public function topic() {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
}
