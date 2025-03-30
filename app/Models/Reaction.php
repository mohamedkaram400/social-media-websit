<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = ['object_id', 'object_type', 'user_id', 'type'];
}
