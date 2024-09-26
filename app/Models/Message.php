<?php

namespace App\Models;

use App\Services\LlmServices\RoleEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'role' => RoleEnum::class,
        'tool_args' => 'array',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeNotTool(Builder $query)
    {
        return $query->where('role', '!=', RoleEnum::Tool->value);
    }

    public function scopeNotSystem(Builder $query)
    {
        return $query->where('role', '!=', RoleEnum::System->value);
    }

    public function scopeNotAutomation(Builder $query)
    {
        return $query->where(function ($query) {
            $query->where('role', '!=', RoleEnum::Assistant->value)
                ->orWhere(function ($query) {
                    $query->where('role', '=', RoleEnum::User->value)
                        ->whereNotNull('user_id');
                });
        });
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
