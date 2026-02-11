<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id',
        'report_date',
        'start_time',
        'end_time',
        'project_name',
        'work_hours',
        'work_content',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'work_hours' => 'integer',
    ];

    /**
     * このレポートを作成したユーザー
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
