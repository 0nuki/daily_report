<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
    ];

    /**
     * このプロジェクトが属するユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このプロジェクトに関連する日報
     */
    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }
}
