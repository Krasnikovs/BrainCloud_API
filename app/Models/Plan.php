<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $primaryKey = 'type';
    public $incrementing = false;
    protected $fillable = [
        'type',
        'description',
        'max_space',
        'price',
    ];
}
