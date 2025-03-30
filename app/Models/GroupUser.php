<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupUser extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'status',
        'role',
        'group_id',
        'token',
        'token_expire_date',
        'user_id',
        'created_by',
    ];
    
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
