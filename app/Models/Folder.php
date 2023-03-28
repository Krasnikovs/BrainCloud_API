<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'user_id',
        'folder_location',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
